<?php

class TestsCompiler 
{
	private $configProvider, $config;
	private $tests = array();
	private $extensions = array('js');

	private $errors = array(
		'noTestsParam' => "Параметр конфигурации <b>tests</b>, содержащий путь к тестам не указан.",
		'testsParamNotString' => "Параметр конфигурации <b>tests</b> присутствует, но не является строкой.",
		'testsDirNotFound' => "Директория, указанная в параметре конфигурации <b>tests</b> не найдена.",
		'noTestsFound' => "В директории с тестами, указанной в параметре конфигурации <b>tests</b>, нет соответствующих файлов со скриптами тестов.<br>Файлы должны иметь расширение <b>JS</b> и содержать код следующего вида:<xmp>test before gotData {\n\tif (!isObject(data)) error('error text');\n}</xmp><xmp>test after onRendered {\n\tif (!this.has('name')) error('error text');\n}</xmp>Для подробной информации по тестам, смотрите соответствующую подсказку",
		'noActiveTestFound' => "Не найдено ни одного активного теста",
		'testFileIncorrectBeginning' => 'Ошибка в файле теста класса {??}. Некорректный код в начале файла',
		'testFileIncorrectCode' => 'Ошибка в файле теста класса {??}. Некорректный код после функции <b>test {?} {?}</b>'
	);

	public function __construct($configProvider) {
		$this->configProvider = $configProvider;
	}

	public function init() {
		$this->config = $this->configProvider->getTestsConfig();
		if (empty($this->config)) {
			new Error($this->errors['noTestsParam']);
		}

		if (!is_string($this->config)) {
			new Error($this->errors['testsParamNotString']);
		}
		if (!is_dir($this->config)) {
			new Error($this->errors['testsDirNotFound']);
		}

		$this->gatherTests($this->config);
		if (empty($this->tests)) {
			new Error($this->errors['noTestsFound']);
		}
		$this->parseTests();
		if (empty($this->tests)) {
			new Error($this->errors['noActiveTestFound']);
		}
	}

	private function gatherTests($dir) {
		$files = scandir($dir);
		if (is_array($files)) {
			foreach ($files as $file) {
				if ($file == '..' || $file == '.') continue;
				$path = $dir."/".$file;
				if (is_dir($path)) {
					$this->gatherTests($path);
				} elseif (file_exists($path)) {
					$pathInfo = pathinfo($path);
					$ext = strtolower($pathInfo['extension']);
    				if (array_search($ext, $this->extensions) !== false) {
						$data = array('class' => $pathInfo['filename'], 'content' => file_get_contents($path));
						$this->tests[] = $data;
					}
				}
			}
		}
	}

	private function parseTests() {
		$regexp = '/(--)*\s*\btest +(after|before) +(\w+)\s*\{/';
		foreach ($this->tests as &$test) {
			$names = array();
			$codes = array();
			$locs = array();
			$funcs = array();
			preg_match_all($regexp, $test['content'], $matches);
			$parts = preg_split($regexp, $test['content']);
			foreach ($parts as $i => $part) {
				$part = trim($part);
				$pureCode = preg_replace('/\s/', '', $part);
				if ($i == 0) {
					if (strtolower($pureCode) == 'exit') {
						break;
					}
					if ($pureCode != '') {
						new Error($this->errors['testFileIncorrectBeginning'], array($test['class']));
					}
					continue;
				}
				$off = !empty($matches[1][$i - 1]);
				if ($off) continue;
				$match = $matches[3][$i - 1];
				$loc = $matches[2][$i - 1];
				if ($pureCode != '') {
					$isExit = preg_match('/exit$/i', $part);
					if (!preg_match('/\}$/', $part) && !$isExit) {
						new Error($this->errors['testFileIncorrectCode'], array($test['name'], $loc, $match));
					}
				}
				$names[] = $loc.' '.$match;
				$codes[] = $this->parseTestFunctionCode(trim($part, '}'), $test['class'], $loc, $match);
				$locs[] = $loc;
				$funcs[] = $match;
				if ($isExit) {
					break;
				}
			}
			unset($test['content']);
			if (!empty($names)) {
				$test['functions'] = array();
				foreach ($codes as $j => $code) {
					$test['functions'][] = array('name' => $funcs[$j], 'loc' => $locs[$j], 'code' => $code);
				}
			}
		}
		$properTests = array();
		foreach ($this->tests as &$test) {
			$properFuncs = array();
			if (!is_array($test['functions'])) continue;
			foreach ($test['functions'] as $f) {
				if (!is_array($properFuncs[$f['name']])) {
					$properFuncs[$f['name']] = array();
				}
				if ($f['loc'] == 'before') {
					$properFuncs[$f['name']]['before'] = $f['code'];
				} elseif ($f['loc'] == 'after') {
					$properFuncs[$f['name']]['after'] = $f['code'];
				}
			}
			$test['functions'] = $properFuncs;
			$properTests[$test['class']] = $test;
		}
		$this->tests = $properTests;
	}

	private function parseTestFunctionCode($code, $className, $loc, $funcName) {
		$texts = array();
		$error = 'Ошибка в файле теста класса <b>'.$className.'</b>. Некорректный код в функции <b>test '.$loc.' '.$funcName.'</b>.';
		$code = transformQuotedText($code, $texts);
		$methods = 'String|Number|Numeric|Bool|Function|Array|Object|ArrayLike|Element|Node|Text|ComponentLike|Component|Control|Null|Undefined|Empty|NotEmptyString|Zero';
		$regexp = '/\b(assert|is)('.$methods.')\(([^\)]+)\)/';
		preg_match_all($regexp, $code, $matches);
		$funcs = $matches[1];
		$types = $matches[2];
		$args = $matches[3];
		if (!empty($matches[0])) {
			$parts = preg_split($regexp, $code);
			$code = '';
			foreach ($parts as $i => $part) {
				$code .= $part;
				if (isset($funcs[$i])) {
					$func = $funcs[$i] == 'is' ? 'check' : 'assert';
					$a = explode(',', $args[$i]);
					$errorLine = '<xmp>'.$matches[0][$i].'</xmp>';
					if ($func == 'check' && count($a) != 1) {
						error($error.' Метод <b>'.$funcs[$i].$types[$i].'</b> должен принимать не более одного аргумента'.$errorLine);
					} elseif ($func == 'assert' && count($a) != 2) {
						error($error.' Метод <b>'.$funcs[$i].$types[$i].'</b> должен принимать не более двух аргументов'.$errorLine);
					}
					$code .= 'Tester.'.$func."('".lcfirst($types[$i])."',".$args[$i].')';
				}
			}
		}
		$code = preg_replace('/\blog\(__TEXT__\)/', "Tester.log(__TEXT__, '".$className."', '".$funcName."')", $code);
		return "\t".restoreTexts($code, $texts);
	}
}