<?php

class UtilsCompiler 
{
	private $configProvider;
	private $functions = array();

	private $errors = array(
		'duplicateFunc' => 'Переопределение системной функции {??} другой функцией в файле {??}',
		'invalidFuncName' => 'Некорректное имя функции {??} в файле {??}. Имя функции утилиты должно начинаться с маленькой буквы'
	);

	public function __construct($configProvider) {
		$this->configProvider = $configProvider;
	}

	public function getFunctionsList() {
		return $this->functions;
	}

	public function run($files, $systemUtilsFuncs) {
		foreach ($files as $file) {
			preg_match_all('/\bfunction\s+(\w+)\s*\(/', $file['content'], $matches);
			$funcs = $matches[1];
			foreach ($funcs as $funcName) {
				if (in_array($funcName, $systemUtilsFuncs)) {
					new Error($this->errors['duplicateFunc'], array($funcName, $file['path']));
				}
				if (!$this->isValidFuncName($funcName)) {
					new Error($this->errors['invalidFuncName'], array($funcName, $file['path']));
				}
			}
			$this->functions = array_merge($this->functions, $funcs);
		}
	}

	private function isValidFuncName($name) {
		return preg_match("/^[a-z]\w*$/", $name);
	}
}