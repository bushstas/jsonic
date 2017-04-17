<?php

class JSCoreRenderer
{
	private $dir = 'jscore';
	private $outputDir = '../core_';

	public function run() {
		if (is_dir($this->$outputDir)) {
			Gatherer::emptyFolder($this->$outputDir);
		} else {
			mkdir($this->$outputDir);
		}
		$items = Gatherer::getItems($this->dir);
	}
}

?>