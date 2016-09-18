<?php

class DeclCompiler 
{	
	private $declensions = array();
	private $regexp = '/@(\w+)\s*:\s*/';

	public function run($declFiles) {
		if (is_array($declFiles)) {
			foreach ($declFiles as $declFile) {
				preg_match_all($this->regexp, $declFile['content'], $matches);
				$varNames = $matches[1];
				if (!empty($varNames)) {
					$parts = preg_split($this->regexp, $declFile['content']);
					array_shift($parts);
					foreach ($parts as $i => $part) {
						$this->declensions[$varNames[$i]] = explode(',', trim($part));
					}
				}
			}
		}
	}
}