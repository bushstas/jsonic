<?php

include 'php/printer.php';
include 'php/errors.php';
include 'php/error.php';
include 'php/iterator.php';
include 'php/parser.php';
include 'php/function.php';
include 'php/keywords.php';


$args = array();
$code = 'arguments var a = 55;var b = fff';

$code = preg_replace('/ {2,}/', ' ', trim($code));
$code = preg_replace('/\t{2,}/', "\t", $code);
$code = preg_replace('/\n{2,}/', "\n", $code);

$parts = preg_split('/\b/', $code);
$properParts = array();
foreach ($parts as $part) {
	if (strlen($part) == 1 || preg_match('/^\w/', $part)) {
		$properParts[] = $part;
	} else {
		for ($i = 0; $i < strlen($part); $i++) {
			$properParts[] = $part[$i];
		}
	}
}
Iteration::init($properParts);

$parser = new FunctionParser();
die($parser->parse($args));
