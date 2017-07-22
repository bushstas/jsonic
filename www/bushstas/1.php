<?php

include 'php/error.php';
include 'php/iterator.php';
include 'php/parser.php';
include 'php/function.php';
include 'php/keywords.php';


$args = array();
$content = 'var a = 55; var b = fff';

$parts = preg_split('/\b/', $content);
Iterator::init($parts);
$parser = new FunctionParser();
die($parser->parse($args));
