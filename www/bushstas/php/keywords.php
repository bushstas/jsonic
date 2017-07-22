<?php

class ArgumentsParser {
	function parse($args) {
		$s = Iterator::next();
		if ($s != '[') {
			Error::unexpected($s, array('[', ' '));
		} else {
			$parser = new ArrayItemParser();
			return Iterator::current(true).$parser->parse($args);
		}
	}
}

class VarParser {
	function parse(&$args) {
		$s = Iterator::isSpace(1);
		if (!$s) {
			Error::unexpected(Iterator::next(), array(' ', "\n"));
		} else {

		}
	}
}