<?php

class FromCodeParser extends OperatorParser
{	
	protected $keywords = array('from', 'to', 'step');
	protected $properKeywords = array('to', 'step');
	protected $improperKeywords = array('from');
	protected $mustHaveKeywords = array('to');
	protected $firstKeyword = 'to';

	protected $operator = 'from';
	protected $codeExample = "Оператор может иметь вид:<xmp>{from &a = 1 to 2}\n{from &i = 0 to 5 step 1}\n{from &n = .getStart() to \$end step &step}</xmp>";

	private $ownErrors = array(
		'noVar' => 'Ошибка парсинга оператора {??} в шаблоне {??} класса {??}<xmp>{{?}}</xmp>{?}',
		'incorrectVar' => 'Ошибка парсинга оператора {??} в шаблоне {??} класса {??}<xmp>{{?}}</xmp>{?}'
	);

	protected function _parse() {
		$this->parseCode('from', array('$', '~', '&', '.', 'a', '0', '!', '(', '-'), '&'.$this->data['var'].' = ');

		foreach ($this->order as $kw) {
			$this->parseCode($kw, array('$', '~', '&', '.', 'a', '0', '!', '(', '-'), $kw);
		}
		
		return $this->data;
	}

	protected function prepareData() {
		$content = &$this->content;
		TextParser::encode($content, 'from');
		$this->validate();	

		$data = Splitter::split('/\b('.implode('|', $this->properKeywords).')\b/', trim($content));
		$this->data['from'] = trim($data['items'][0]);
		if (empty($this->data['from'])) {
			new Error($this->ownErrors['noVar'], array($this->operator, $this->templateName, $this->className, $this->operator.' '.$this->content, $this->codeExample));
		}
		$regex = '/^&([a-z]\w*)\s*=/';
		preg_match($regex, $this->data['from'], $match);
		if (empty($match[1])) {
			new Error($this->ownErrors['incorrectVar'], array($this->operator, $this->templateName, $this->className, $this->operator.' '.$this->content, $this->codeExample));	
		}
		$this->data['var'] = $match[1];
		$this->data['from'] = preg_replace($regex, '', $this->data['from']);
		$keywords = $data['delimiters'];
		for ($i = 1; $i < count($data['items']); $i++) {
			$kw = $keywords[$i - 1];
			$vl = $data['items'][$i];
			$this->data[$kw] = trim($vl);
		}

		$parts = array($this->data['from']);
		$properKeywords = array('from');
		foreach ($keywords as $kw) {
			$this->order[] = $kw;
			$properKeywords[] = $kw; 
			$parts[] = $this->data[$kw];
		}
		$content = implode($this->D, $parts);
		TextParser::decode($content, 'from');
		$parts = explode($this->D, $content);		
		foreach ($parts as $i => $part) {
			$this->data[$properKeywords[$i]] = $part;
		}
	}
}