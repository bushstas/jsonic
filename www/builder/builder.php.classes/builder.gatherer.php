<?php

class Gatherer 
{
	private $configProvider, $config;
	private $extensions = array(
		'js', 'css', 'template', 'texts', 'data', 'cssconst', 'include', 'decl'
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
		
		$files = array(
			'core' => $this->core
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
							$data = array(
								'path' => $path,
								'ext' => $ext,
								'filename' => $file,
								'name' => $path_info['filename'],
								'content' => file_get_contents($path)
							);
							$list[] = $data;
						}
					}
				}
			}
		}
		return $list;
	}

	public static function createFile($path, $content) {
		$parts = explode('/', $path);
		if (count($parts) > 1) {
			$parts[count($parts) - 1] = '';
			$pathToFolder = implode('/', $parts);
			if (!is_dir($pathToFolder)) {
				self::createDir($pathToFolder);
			}
		}
		file_put_contents($path, $content);
	}

	public static function createDir($path) {
		$parts = explode('/', trim($path));
		$path = array();
		foreach ($parts as $part) {
			$path[] = $part;
			if (!empty($part) && $part != '.' && $parts != '..') {
				$currentPath = implode('/', $path);
				if (!is_dir($currentPath)) {
					mkdir($currentPath);
				}
			}
		}
	}
}