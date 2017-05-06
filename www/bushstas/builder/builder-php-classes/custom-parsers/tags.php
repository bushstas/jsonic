<?php

	// priority 6
	
	$getTagClassName = function() use ($className) {
		$data = Splitter::split('/[A-Z]/', $className);
		$className = '';
		foreach ($data['items'] as $i => $item) {
			$className .= $item.'-';
			if (isset($data['delimiters'][$i])) {
				$className .= strtolower($data['delimiters'][$i]);
			}
		}
		return trim($className, '-');
	};
	
	$content = preg_replace('/<::(\w+)> *<>/', "<::$1>.getElement()", $content);
	$content = str_replace('<>', ' this.getElement()', $content);
	$regexp = '/[\w\]\[\.]*<[\.\#:]*[@a-z][\w\-\.\#\]\[]*>/i';
	$parts = preg_split($regexp, $content);
	preg_match_all($regexp, $content, $matches);
	$matches = $matches[0];
	$content = '';
	foreach ($parts as $i => $part) {
		$content .= $part;
		if (isset($matches[$i])) {
			$p = preg_split('/[<>]/', $matches[$i]);
			$tag = $p[1];
			$scope = '';
			$index = null;
			if ($p[0] == 'return') {
				$content .= 'return ';
			} elseif (!empty($p[0])) {
				$scope = ','.$p[0];
			}
			$p = explode('[', $tag);
			if (isset($p[1])) {
				$tag = $p[0];
				$p = explode(']', $p[1]);
				if (isset($p[1])) {
					$index = $p[0];
				}
			}
			$tag = preg_replace('/[^\.\#:\-\w@]/', '', $tag);
			preg_match_all('/([\.\#:]*)([@\w\-\.\#]+)/', $tag, $ms);
			$className = $ms[2][0];
			if ($ms[1][0] == ':') {
				$content .= " this.getElement('".$className."')";
			} elseif ($ms[1][0] == '::') {
				$content .= " this.getChild('".$className."')";
			} else {					
				if ($className[0] == '@') {
					if ($className != '@') { 
						$className = $getTagClassName().'_'.ltrim($className, '@');
					} else {
						$className = $getTagClassName();
					}
				}

				$selector = !empty($ms[1][0]) ? $ms[1][0].'->>' : '';
				$parts = explode('.', $className);
				$className = $parts[0];
				for ($j = 1; $j < count($parts); $j++) {
					$className .= '.->>'.$parts[$j];
				}
				if ($index === null) {
					$content .= " this.findElement('".$selector.$className.$scope."')";
				} elseif(empty($index)) {
					$content .= " this.findElements('".$selector.$className.$scope."')";
				} else {
					$content .= " this.findElements('".$selector.$className.$scope."')[".$index."]";
				}
			}
		}
	}
	

