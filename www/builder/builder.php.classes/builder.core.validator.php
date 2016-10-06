<?php

class CoreValidator 
{	
	private $errors = array(
		'noCoreFolder' => 'Директория скриптов ядра {??} не обнаружена. Путь к диретории: {??}',
		'noCoreFile' => 'Файл {??} в директории {??} не обнаружен. Путь к файлу: {??}'
	);

	private $content = array(
		'components' => array(
			'Application', 'Component', 'Condition', 'Control', 'Controller', 'Foreach', 'IfSwitch', 'Level', 'Menu', 'Switch', 'View'
		),
		'helpers' => array(),
		'prototypes' => array(
			'Array', 'Element', 'MouseEvent', 'String'
		),
		'services' => array(
			'Core', 'AjaxRequest', 'Corrector', 'EventHandler', 'Router', 'Tester', 'User'
		),
		'utils' => array(
			'utils', 'Objects'
		)
	);
	public function validate($pathToCore) {
		$pathToCore = rtrim($pathToCore, '/').'/';
		foreach ($this->content as $folder => $files) {
			if (!is_dir($pathToCore.$folder)) {
				new Error($this->errors['noCoreFolder'], array($folder, $pathToCore.$folder));
			}
			foreach ($files as $file) {
				if (!file_exists($pathToCore.$folder.'/'.$file.'.js')) {
					new Error($this->errors['noCoreFile'], array($file.'.js', $folder, $pathToCore.$folder.'/'.$file.'.js'));
				}
			}
		}
	}
}