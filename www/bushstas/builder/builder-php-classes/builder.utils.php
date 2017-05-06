<?php

class UtilsCompiler 
{
	private $functions = array();
	private $regexp = '/function\s+[a-zA-Z_]\w*\s*\([^\)]*\)\s*\{|\{|\}/';

	private $errors = array(
		'overrideFunc' => 'Обнаружена попытка переопределения системной функции {??} функцией утилитой в файле {??}',
		'duplicateFunc' => 'Обнаружены функции утилиты с одинаковыми именами {??} в файле {??}',
		'duplicateFunc2' => 'Обнаружены функции утилиты с одинаковыми именами {??} в файлах {??} и {??}',
		'invalidFuncName' => 'Некорректное имя функции {??} в файле {??}<br>Имя функции утилиты должно начинаться с маленькой буквы',
		'codeBetweenFuncs' => 'Обнаружен недопустимый код вне функций в файле {??}:<xmp>{?}</xmp>',
		'funcInFunc' => "Обнаружена вложенная функция {??} внутри функции {??} в файле {??}.<br><br>Для замыканий используйте код вида:<xmp>function utilsFunc() {\n\t var innerFunc = function() {\n\n\n\t}\n}</xmp>"
	);

	public function getFunctions() {
		return $this->functions;
	}

	public function getFunctionsList() {
		return array_keys($this->functions);
	}

	public function run($files, $systemUtilsFuncs) {
		$funcsByFiles = array();
		foreach ($files as $file) {
			$data = Splitter::split($this->regexp, $file['content']);
			extract($data);
			$funcOpen = false;
			$openBrackets = 0;
			$openFuncName = '';
			$funcNames = array();
			$funcContent = '';
			$openFuncArgs = '';
			foreach ($items as $i => $item) {
				$it = preg_replace('/\s/', '', $item);
				if (!empty($it)) {
					if (!$funcOpen) {
						new Error($this->errors['codeBetweenFuncs'], array($file['path'], $item));
					}
					$funcContent .= $item;
				}
				if (isset($delimiters[$i])) {
					$d = $delimiters[$i];
					if ($d[0] == 'f') {
						preg_match_all('/function\s+([a-zA-Z_]\w*)\s*\(([^\)]*)/', $d, $matches);
						$funcName = $matches[1][0];
						if ($funcOpen) {
							new Error($this->errors['funcInFunc'], array($funcName, $openFuncName, $file['path']));
						} else {
							if (preg_match('/^[A-Z_]/', $funcName)) {
								new Error($this->errors['invalidFuncName'], array($funcName, $file['path']));
							}
							if (isset($funcNames[$funcName])) {
								new Error($this->errors['duplicateFunc'], array($funcName, $file['path']));
							}
							if (isset($funcsByFiles[$funcName])) {
								new Error($this->errors['duplicateFunc2'], array($funcName, $file['path'], $funcsByFiles[$funcName]));
							}
							if (in_array($funcName, $systemUtilsFuncs)) {
								new Error($this->errors['overrideFunc'], array($funcName, $file['path']));
							}
							$openFuncName = $funcName;
							$openFuncArgs = $matches[2][0];
							$funcNames[$funcName] = 1;
							$funcsByFiles[$funcName] = $file['path'];
						}
						$funcOpen = true;
						$openBrackets++;
					} elseif ($d == '{') {
						$openBrackets++;
					} else {
						$openBrackets--;
						if ($openBrackets == 0) {
							$funcOpen = false;
							$this->functions[$openFuncName] = array(
								'code' => $funcContent,
								'args' => $openFuncArgs
							);
							$openFuncName = '';
							$funcContent = '';
						}
					}
				}
			}
		}
	}
}