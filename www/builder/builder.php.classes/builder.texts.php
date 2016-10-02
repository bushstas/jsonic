<?php

class TextsCompiler 
{	
	private $textsIndex = array();
	private $textsConstants = array();
	private $nameFiles = array();
	private $regexp = '/@(\w+)\s*:\s*/';

	private $errors = array(
		'duplicateInFile' => 'Дублирование текстовой контстанты {??} в файлe {??}.texts',
		'duplicateInFiles' => 'Дублирование текстовой контстанты {??} в файлах {??}.texts и {??}.texts'
	);

	public function run($textsFiles) {
		if (!empty($textsFiles)) {
			foreach ($textsFiles as $textFile) {
				$text = $textFile['content'];
				$file = $textFile['name'];
				preg_match_all($this->regexp, $text, $matches);
				$varNames = $matches[1];
				if (!empty($varNames)) {
					$parts = preg_split($this->regexp, $text);
					array_shift($parts);
					foreach ($parts as $i => $part) {
						if (!empty($this->textsConstants[$varNames[$i]])) {
							if ($this->nameFiles[$varNames[$i]] == $file) {
								new Error($this->errors['duplicateInFile'], array($varNames[$i], $this->nameFiles[$varNames[$i]]));
							} else {
								new Error($this->errors['duplicateInFiles'], array($varNames[$i], $this->nameFiles[$varNames[$i]], $file));
							}
						}
						$this->nameFiles[$varNames[$i]] = $file;
						$this->textsConstants[$varNames[$i]] = trim($part);
					}
				}
			}
			$this->textsIndex = array_keys($this->textsConstants);
			$this->textsConstants = array_values($this->textsConstants);
		}
	}

	public function get() {
		return array(
			'index' => $this->textsIndex,
			'texts' => $this->textsConstants,
			'files' => $this->nameFiles
		);
	}
}