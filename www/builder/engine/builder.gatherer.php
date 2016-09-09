<?php

class Gatherer 
{
	private $configProvider, $config;
	private $extensions = array(
		'js', 'css', 'template', 'texts', 'data', 'cssconst', 'include', 'decl'
	);
	private $errors = array(
		'noPathToSrc' => 'Директория указанная в параметре <b>scope</b> не найдена',
		'noPathToCore' => 'Директория указанная в параметре <b>sources</b> не найдена',
		'testDirInScope' => 'Директория с тестами указанная в файле конфигурации {??} должна располагаться вне директории с исходными кодами {??}',
		'scriptsDirInScope' => 'Директория со сторонними скриптами указанная в файле конфигурации {??} должна располагаться вне директории с исходными кодами {??}'
	);

	public function __construct($configProvider) {
		$this->configProvider = $configProvider;
	}

	public function init() {
		$this->config = $this->configProvider->getGathererConfig();
		if (!is_dir($this->config['pathToCore'])) {
			$this->showError('noPathToCore');
		}
		if (!is_dir($this->config['pathToSrc'])) {
			$this->showError('noPathToSrc');
		}
		if (empty($this->config['pathToTests'])) {
			$this->config['pathToTests'] = '';
		}
		if (empty($this->config['pathToScripts'])) {
			$this->config['pathToScripts'] = '';
		}
	}

	public function run() {
		$this->core = $this->gather($this->config['pathToCore']);
		$this->sources = $this->gather($this->config['pathToSrc']);
	}

	private function gather($dir) {
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
							$this->showError('testDirInScope', array($testsPath, $this->config['pathToSrc']));
						}
						if ($cleanPath == $scriptsPath) {
							$this->showError('scriptsDirInScope', array($scriptsPath, $this->config['pathToSrc']));
						}
						$list = gatherFiles($path, $list, $getContent);
					} elseif (file_exists($path)) {
						$path_info = pathinfo($path);
						$ext = strtolower($path_info['extension']);
    					if (array_search($ext, $extensions) !== false) {
							$data = array('path' => $path, 'ext' => $ext, 'filename' => $file, 'name' => $path_info['filename']);
							if ($getContent === true) {
								$data['content'] = file_get_contents($data['path']);
							}
							$list[] = $data;
						}
					}
				}
			}
		}
		return $list;
	}

	public function showError($err, $args = null) {
		new Error($this->errors[$err], $args);
	}
}