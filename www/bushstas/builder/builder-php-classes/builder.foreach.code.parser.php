<?php

class ForeachCodeParser extends OperatorParser
{	
	protected $keywords = array('as', 'limit', 'while', 'from', 'to', 'right', 'random', 'foreach');
	protected $properKeywords = array('as', 'limit', 'while', 'from', 'to');
	protected $improperKeywords = array('right', 'random', 'foreach');
	protected $mustHaveKeywords = array('as');
	protected $firstKeyword = 'as';
	protected $codeExample = "Оператор может иметь вид:<xmp>{foreach ~items as &item}\n{foreach \$items as &idx => &item}\n{foreach .getItems() as &i => &item limit 10}\n{foreach &items as &i => &item from 2 to 7}\n{foreach right \$a as &i => &item while &i > 6}\n{foreach random \$items as &idx => &item}</xmp>";

	private $ownErrors = array(
		'noItems' => "Ошибка парсинга оператора {??} в шаблоне {??} класса {??}<xmp>{{?}}</xmp>Отсутсвует сущность для перебора в цикле\n{?}",
		'incorrectVar' => 'Ошибка парсинга оператора {??} в шаблоне {??} класса {??}<xmp>{{?}}</xmp>Некорректное обозначение переменной<xmp>{?}</xmp>{?}'
	);

	protected $operator = 'foreach';

	protected function _parse() {
		$this->parseCode('items', array('$', '~', '&', '.', 'a', '(', '!'), '', 'as');
		$this->parseCode('key', array('&'), '', '=>');
		$this->parseCode('value', array('&'));

		foreach ($this->order as $kw) {
			$this->parseCode($kw, array('0', 'a', '$', '~', '&', '.', '!', '(', '-'), $kw);
		}
		return $this->data;
	}

	protected function prepareData() {
		$content = &$this->content;
		TextParser::encode($content, 'foreach');
		if (preg_match('/^right\b/', $content)) {
			$this->data['right'] = true;
			$content = preg_replace('/^right\s*/', '', $content);
		}
		if (preg_match('/^random\b/', $content)) {
			$this->data['random'] = true;
			$content = preg_replace('/^random\s*/', '', $content);
		}
		$this->validate();	

		$data = Splitter::split('/\b('.implode('|', $this->properKeywords).')\b/', trim($content));
		$this->data['items'] = $data['items'][0];
		if (empty($this->data['items'])) {
			new Error($this->ownErrors['noItems'], array($this->operator, $this->templateName, $this->className, $this->operator.' '.$this->content, $this->codeExample));
		}
		$keywords = $data['delimiters'];
		for ($i = 1; $i < count($data['items']); $i++) {
			$kw = $keywords[$i - 1];
			$vl = $data['items'][$i];
			$this->data[$kw] = trim($vl);
		}
		$parts = explode('=>', $this->data['as']);
		if (count($parts) == 1) {
			$this->data['value'] = $parts[0];
		} else {
			$this->data['key'] = $parts[0];
			$vals = array();
			for ($i = 1; $i < count($parts); $i++) {
				$vals[] = $parts[$i];
			}
			$this->data['value'] = implode('=>', $vals);
		}
		if (!empty($this->data['key']) && !preg_match('/^[&\$~\#@][a-z]\w*$/i', trim($this->data['key']))) {
			new Error($this->ownErrors['incorrectVar'], array($this->operator, $this->templateName, $this->className, $this->operator.' '.$this->content, $this->data['key'], $this->codeExample));
		}
		if (!empty($this->data['value']) && !preg_match('/^[&\$~\#@][a-z]\w*$/i', trim($this->data['value']))) {
			new Error($this->ownErrors['incorrectVar'], array($this->operator, $this->templateName, $this->className, $this->operator.' '.$this->content, $this->data['value'], $this->codeExample));
		}

		$parts = array($this->data['items']);
		$properKeywords = array('items');
		foreach ($keywords as $kw) {
			if ($kw == 'as') {
				array_push($properKeywords, 'key', 'value');
				array_push($parts, $this->data['key'], $this->data['value']);
			} else {
				$this->order[] = $kw;
				$properKeywords[] = $kw; 
				$parts[] = $this->data[$kw];
			}
		}
		$content = implode($this->D, $parts);
		TextParser::decode($content, 'foreach');
		$parts = explode($this->D, $content);		
		foreach ($parts as $i => $part) {
			$this->data[$properKeywords[$i]] = $part;
		}
	}
}