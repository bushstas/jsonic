<?php

class Gatherer 
{
	private $configProvider, $config;
	private $extensions = array(
		'js', 'css', 'template', 'texts', 'data', 'cssconst', 'include', 'decl', 'utils', 'tags'
	);
	private $errors = array(
		'noPathToSrc' => 'Директория указанная в параметре <b>scope</b> не найдена',
		'noPathToCore' => 'Директория указанная в параметре <b>core</b> не найдена',
		'testDirInScope' => 'Директория с тестами указанная в файле конфигурации {??} должна располагаться вне директории с исходными кодами {??}',
		'scriptsDirInScope' => 'Директория со сторонними скриптами указанная в файле конфигурации {??} должна располагаться вне директории с исходными кодами {??}'
	);

	public function __construct($configProvider) {
		$this->configProvider = $configProvider;
	}

	public function init() {
		$this->config = $this->configProvider->getGathererConfig();
		if (!is_dir($this->config['pathToCore'])) {
			new Error($this->errors['noPathToCore']);
		}
		if (!is_dir($this->config['pathToSrc'])) {
			new Error($this->errors['noPathToSrc']);
		}
		if (empty($this->config['pathToTests'])) {
			$this->config['pathToTests'] = '';
		}
		if (empty($this->config['pathToScripts'])) {
			$this->config['pathToScripts'] = '';
		}
	}

	public function gatherFiles() {
		
		$this->core = array();
		$this->gather($this->config['pathToCore'], $this->core);
		$this->sources = array();
		$this->gather($this->config['pathToSrc'], $this->sources);
		$this->scripts = array();
		$this->gather($this->config['pathToScripts'], $this->scripts);
		
		$files = array(
			'core' => $this->core,
			'scripts' => $this->scripts
		);

		foreach ($this->sources as $file) {
			if (!is_array($files[$file['ext']])) {
				$files[$file['ext']] = array();
			}
			$files[$file['ext']][] = $file;
		}
		return $files;
	}

	private function gather($dir, &$list) {
		$testsPath = preg_replace('/^\.\//', '', $this->config['pathToTests']);
		$scriptsPath = preg_replace('/^\.\//', '', $this->config['pathToScripts']);
		if (is_dir($dir)) {
			$files = scandir($dir);
			if (is_array($files)) {
				foreach ($files as $file) {
					if ($file == '..' || $file == '.') continue;
					$path = $dir."/".$file;
					if (is_dir($path)) {
						$cleanPath = preg_replace('/^\.\//', '', $path);
						if ($cleanPath == $testsPath) {
							new Error($this->errors['testDirInScope'], array($testsPath, $this->config['pathToSrc']));
						}
						if ($cleanPath == $scriptsPath) {
							new Error($this->errors['scriptsDirInScope'], array($scriptsPath, $this->config['pathToSrc']));
						}
						$this->gather($path, $list);
					} elseif (file_exists($path)) {
						$path_info = pathinfo($path);
						$ext = strtolower($path_info['extension']);
    					if (array_search($ext, $this->extensions) !== false) {
    						$content = file_get_contents($path);
							$data = array(
								'path' => $path,
								'ext' => $ext,
								'filename' => $file,
								'name' => $path_info['filename'],
								'content' => $this->correct($content, $ext)
							);
							$list[] = $data;
						}
					}
				}
			}
		}
		return $list;
	}

	private function correct($content, $ext) {
		$encoded = false;
		if ($ext == 'js') {
			$content = preg_replace("/\/\*[\S\s]*?\*\//", "", $content);
			if (preg_match('/\/\*/', $content)) {
				$encoded = true;
				TextParser::encode($content, 'gatherer');
			}
			$content = preg_replace("/\/\*[\S\s]*/", "", $content);
			$content = preg_replace("/\n\s*\/\/[^\n]*/", "\n", $content);
		} elseif ($ext == 'template') {
			$content = preg_replace("/<\!--[\S\s]*?-->/", '', $content);
			$content = preg_replace("/<\!--[\S\s]*/", '', $content);
		}
		if ($encoded) {
			TextParser::decode($content, 'gatherer');
		}
		return $content;
	}

	public static function getFiles($dir, $ext = '', $recursive = false) {
		$files = array();
		if (is_dir($dir)) {
			$fs = scandir($dir);
			if (is_array($fs)) {
				foreach ($fs as $file) {
					if ($file == '..' || $file == '.' || (!empty($ext) && !preg_match('/\.'.$ext.'$/i', $file))) continue;
					$path = $dir."/".$file;
					if (is_dir($path)) {
						if ($recursive === true) {
							$files = array_merge($files, self::getFiles($path, $ext, true));
						}
					} else {
						$files[] = $path;
					}
				}
			}
		}
		return $files;
	}
}