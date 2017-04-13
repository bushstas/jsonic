<?php

class OperatorParser
{	
	protected $data = array(), $templateName, $className;
	protected $code, $content, $order, $operator;
	protected $D = '-||-';

	protected $errors = array(
		'unknownKeyword' => 'Неизвестное ключевое слово {??} в операторе {??} в шаблоне {??} класса {??}<xmp>{{?}}</xmp>{?}',
		'improperKeyword' => 'Ключевое слово {??} в неподходящем месте в операторе {??} в шаблоне {??} класса {??}<xmp>{{?}}</xmp>{?}',
		'extraKeyword' => 'Лишнее повторяющееся ключевое слово {??} в операторе {??} в шаблоне {??} класса {??}<xmp>{{?}}</xmp>{?}',
		'shouldHaveKeyword' => 'Обязательное ключевое слово {??} отсутствует в операторе {??} в шаблоне {??} класса {??}<xmp>{{?}}</xmp>{?}'
	);
	protected $keywords = array();
	protected $properKeywords = array();
	protected $improperKeywords = array();
	protected $mustHaveKeywords = array();
	protected $firstKeyword = '';
	protected $codeExample = '';

	public function parse($content, $templateName, $className) {
		$this->templateName = $templateName;
		$this->className = $className;		
		$this->content = preg_replace('/^'.$this->operator.'\s*/', '', $content);
		$this->order = array();

		TemplateSyntaxParser::init($this->operator, $this->templateName, $this->className);
		$this->code = $this->operator;
		$this->prepareData();
		return $this->_parse();
	}

	protected function addData($data) {
		foreach ($data as $k => $v) {
			if ($k == 'c') continue;
			if (!is_array($this->data[$k])) {
				$this->data[$k] = $v;
			} else {
				foreach ($v as $i) {
					if (!in_array($i, $this->data[$k])) {
						$this->data[$k][] = $i;
					}
				}
			}
		}
	}

	protected function parseCode($param, $symbols, $before = '', $after = '') {
		$item = &$this->data[$param];
		if (!empty($item)) {
			if (!empty($before)) {
				$this->code .= ' '.$before;
			}
			$data = TemplateSyntaxParser::parse($item, $symbols, $this->code);
			$this->code .= ' '.$item.(!empty($after) ? $after.' ' : '');
			$this->addData($data);
			$item = $data['c'];
		}
	}

	protected function validate() {
		$content = ' '.$this->content.' ';
		preg_match_all('/[\s\)\]]([a-z]{2,})(?=[^_a-z\d\(])/i', $content, $matches);
		$foundKeywords = $matches[1];
		foreach ($foundKeywords as $kw) {
			if (!in_array($kw, $this->keywords) && $kw != $textMark) {
				new Error($this->errors['unknownKeyword'], array($kw, $this->operator, $this->templateName, $this->className, $this->operator.' '.$this->content, $this->codeExample));
			}
		}
		foreach ($this->mustHaveKeywords as $kw) {
			if (!in_array($kw, $foundKeywords)) {
				new Error($this->errors['shouldHaveKeyword'], array($kw, $this->operator, $this->templateName, $this->className, $this->operator.' '.$this->content, $this->codeExample));
			}
		}
		if ($foundKeywords[0] != $this->firstKeyword) {
			new Error($this->errors['improperKeyword'], array($foundKeywords[0], $this->operator, $this->templateName, $this->className, $this->operator.' '.$this->content, $this->codeExample));
		}
		$textMark = TextParser::getMark();
		$used = array();
		foreach ($foundKeywords as $kw) {
			if (in_array($kw, $this->improperKeywords)) {
				new Error($this->errors['improperKeyword'], array($kw, $this->operator, $this->templateName, $this->className, $this->operator.' '.$this->content, $this->codeExample));
			}
			if (isset($used[$kw])) {
				new Error($this->errors['extraKeyword'], array($kw, $this->operator, $this->templateName, $this->className, $this->operator.' '.$this->content, $this->codeExample));	
			}
			$used[$kw] = true;
		}	
	}

	protected function _parse() {}
	protected function prepareData() {}
}