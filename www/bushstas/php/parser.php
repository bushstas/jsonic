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
		while (Iterator::has()) {
			$part = Iterator::current();
			if ($part !== '') {
				if (is_numeric($part)) {
					$this->handleNumber($part);
				} elseif (preg_match('/[a-z]/i', $part[0])) {
					if (isset($this->keywords[$part])) {
						$this->handleName($part);
					} else {
						$this->handleKeyword($part);
					}
				} elseif (is_numeric($part[0]) && !$this->isQuoted) {
					$this->throwIncorrectNameError($part);
				} else {
					for ($i = 0; $i < strlen($part); $i++) {
						$this->handleSymbol($part[$i]);
					}
				}
			}
			Iterator::add();
		}
		return $this->code;
	}

	protected function handleNumber($a) {

	}

	protected function handleName($a) {

	}

	protected function handleKeyword($a) {
		$parser = $this->keywords[$a];
		if (!empty($parser) && class_exists($parser)) {
			$parser = new $parser();
			$this->addCode($parser->parse($this->args));
		}
	}

	protected function handleSymbol($a) {

	}

	protected function throwIncorrectNameError($a) {

	}

	protected function addCode($code) {
		$this->code .= $code;
	}
}