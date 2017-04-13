<?php

class SwitchCodeParser extends OperatorParser
{	
	protected $keywords = array('switch');
	protected $improperKeywords = array('switch');

	protected $operator = 'switch';
	protected $codeExample = "Оператор должен иметь вид:<xmp>{switch \$a}\n{switch .getValue()}\n{switch &a}\n{switch ~a}</xmp>";


	protected function _parse() {
		$this->parseCode('switch', array('$', '~', '&', '.', 'a', '0', '!', '(', '-', '"', "'"));
		return $this->data;
	}

	protected function prepareData() {
		$this->validate();	
		$this->data['switch'] = $this->content;
	}
}