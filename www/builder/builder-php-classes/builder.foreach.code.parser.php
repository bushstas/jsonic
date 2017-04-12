<?php

class ForeachCodeParser extends OperatorParser
{	
	protected $keywords = array('as', 'limit', 'while', 'from', 'to', 'right', 'random', 'foreach');
	protected $properKeywords = array('as', 'limit', 'while', 'from', 'to');
	protected $improperKeywords = array('right', 'random', 'foreach');
	protected $mustHaveKeywords = array('as');
	protected $firstKeyword = 'as';

	protected $operator = 'foreach';

	protected function _parse() {
		$this->parseCode('items', array('$', '~', '&', '.', 'a', '(', '!'), '', 'as');
		$this->parseCode('key', array('&'), '', '=>');
		$this->parseCode('value', array('&'));

		foreach ($this->order as $kw) {
			$this->parseCode($kw, array('$', '~', '&', '.', 'a', '0', '!', '(', '-'), $kw);
		}
		return $this->data;
	}

	protected function prepareData() {
		$content = &$this->content;
		TextParser::encode($content, 'foreach');
		$content = preg_replace('/^foreach\s*/', '', $content);
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
			new Error($this->errors['noItems'], array($this->operator, $this->templateName, $this->className, $this->content));
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