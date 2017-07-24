<?php

class Parser {
	protected $isQuoted = false;
	protected $code = '';
	protected $args;
	protected $keywords = array(
		'abstract' => '', 'arguments' => 'ArgumentsParser', 'await' => '', 'boolean' => '', 'break' => '', 'byte' => '', 'case' => '', 'catch' => '', 'char' => '', 'class' => '', 'const' => '', 'continue' => '',
		'debugger' => '', 'default' => '', 'delete' => '', 'do' => '', 'double' => '', 'else' => '', 'enum' => '', 'eval' => '', 'export' => '', 'extends' => '', 'false' => '', 'final' => '',
		'finally' => '', 'float' => '', 'goto' => '', 'implements' => '', 'import' => '', 'in' => '', 'instanceof' => '', 'int' => '', 'interface' => '', 'let' => '', 'long' => '', 'native' => '',
		'null' => '', 'package' => '', 'private' => '', 'protected' => '', 'public' => '', 'return' => '', 'short' => '', 'static' => '', 'super' => '', 'synchronized' => '', 'throw' => '',
		'throws' => '', 'transient' => '', 'true' => '', 'try' => '', 'typeof' => '', 'var' => 'VarParser', 'void' => '', 'volatile' => '', 'while' => '', 'yield' => '', 'if' => '', 'for' => '', 'function' => '',
		'try' => '', 'this' => '', 'new' => '', 'switch' => '', 'with' => ''
	);


	public function parse($args) {
		$this->args = $args;
		$this->beforeParse();
		Iteration::add();
		while (Iteration::has()) {
			$part = Iteration::current();
			if ($part !== '') {
				if (is_numeric($part)) {
					$this->handleNumber($part);
				} elseif (preg_match('/[a-z]/i', $part[0])) {
					if (!isset($this->keywords[$part])) {
						$this->handleName($part);
					} else {
						$this->handleKeyword($part);
					}
				} elseif (is_numeric($part[0]) && !$this->isQuoted) {
					$this->throwIncorrectNameError($part);
				} else {
					$this->handleSymbol($part);
				}
			}
			Iteration::add();
		}
		return $this->code;
	}

	protected function handleNumber($a) {

	}

	protected function handleName($a) {

	}

	protected function handleKeyword($a) {
		$parserName = $this->keywords[$a];
		if (!empty($parserName) && class_exists($parserName)) {
			$parser = new $parserName();
			$this->addCode($parser->parse($this->args));
			switch ($parserName) {
				case 'VarParser':
					$this->args = $parser->getArgs();
				break;
			}
		}
	}

	protected function handleSymbol($a) {

	}

	protected function throwIncorrectNameError($a) {

	}

	protected function addCode($code) {
		$this->code .= $code;
	}

	protected function getArgs() {
		return $this->args;
	}

	protected function beforeParse() {}
}