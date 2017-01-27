<?php

	define('FOLDER', __DIR__.'/builder-php-classes');
	define('IS_TEST', !empty($_GET['istest']));
	define('PATH_TO_JS_MAP', FOLDER.'/data/js.map.php');
	define('PATH_TO_CSS_CONSTS', FOLDER.'/data/css.const.php');