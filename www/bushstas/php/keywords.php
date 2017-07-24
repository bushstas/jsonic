<?php

class ArgumentsParser {
	function parse($args) {
		$s = Iteration::next();
		if ($s != '[') {
			Error::unexpected($s, array('[', ' '));
		} else {
			$parser = new ArrayItemParser();
			return Iteration::current(true).$parser->parse($args);
		}
	}
}

class VarParser extends Parser {

	function __construct() {
		$this->expected = array('space');
	}

	function beforeParse() {
		if (!Iteration::isSpace(1)) {
			Error::unexpected(Iteration::next(), array(' ', "\n"));
		}		
	}

	function handleName($a) {
		if (in_array($a, $this->args)) {
			Error::show('var_ovr', $a);
		}
		$this->args[] = $a;
		parent::handleName($a);
	}
}