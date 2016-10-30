<?php

class JSInterpreter 
{
	public static function parse(&$content, $className) {
		self::prepareCode($content);
		self::parseDataConstants($content);
		self::parseEachOperators($content);
		self::parseDelayOperators($content);
		self::parseIfShortcuts($content);	
		self::parseArrayPushOperators($content);
		self::parseGetDataShortcuts($content);
		self::parseObjectGets($content);
		self::parseDialogShortcuts($content);
		self::parseDispatchEventShortcuts($content);
		self::parseTagShortcuts($content);		
		self::cleanCode($content);
	}

	private static function parseDataConstants(&$content) {
		$content = preg_replace('/\#([a-z]\w*)/i', "_DATA_#$1", $content);
	}

	private static function parseEachOperators(&$content) {
		$itemsName = '_items';
		$names = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','_');		
		foreach ($names as $name) {
			if (!preg_match('/\b'.$name.'\b/', $content)) {
				$itemsName = $name;
				break;
			}
		}
		$content = preg_replace('/\beach\s*\(([\w\.\[\]]+) +as +(\w+)\)\s*\{/', "var ".$itemsName."=$1,idx;for(idx=0;idx<".$itemsName.".length;idx++){var $2=".$itemsName."[idx];", $content);
	}

	private static function parseArrayPushOperators(&$content) {
		$content = preg_replace('/([\w\]\)]) *\[\] *= *([^;\n]+)/i', "$1.push($2)", $content);	
	}

	private static function parseGetDataShortcuts(&$content) {
		$content = preg_replace('/([>\)\]\w]) *-> *(\w+)/', "$1.getData('$2')", $content);	
	}

	private static function parseObjectGets(&$content) {
		$regexp = '/\b(let|const|var) *\{/';
		$hasSimilarNativeCode = preg_match($regexp, $content);
		if ($hasSimilarNativeCode) {
			$content = preg_replace($regexp, "#_$1_#", $content);
		}
		$content = preg_replace('/(\$*[\w\]\[\. ]+) *\{ *([\w\]\[\., ]+) *\}/', " Objects.get($1,$2)", $content);
		if ($hasSimilarNativeCode) {
			$content = preg_replace('/\#_(let|const|var)_\#/', "$1{", $content);	
		}
	}

	private static function parseIfShortcuts(&$content) {
		$regexp = '/@\(([^\)]+)\)/';
		preg_match_all($regexp, $content, $matches);
		$matches = $matches[1];
		if (!empty($matches)) {
			$parts = preg_split($regexp, $content);
			$content = '';
			$regex = '/[\n;]/';
			for ($i = 0; $i < count($parts); $i++) {
				$part = $parts[$i];
				if (isset($parts[$i + 1])) {
					$vars = array($matches[$i]);
					$data = Splitter::split($regex, $part);
					$beginning = $data['items'][count($data['items']) - 1];
					$data['items'][count($data['items']) - 1] = '';				
					$part = Splitter::join($data['items'], $data['delimiters']);
					$content .= $part;
					$count = 0;
					$idx = $i;
					$delimiters = array($beginning);
					while (true) {
						$count++;
						$j = $idx + $count;
						$next = $parts[$j];
						if (isset($next) && !preg_match($regex, $next)) {
							$i++;
							$vars[] = $matches[$i];
							$delimiters[] = $next;
						} else {
							if (!empty($vars)) {
								$line = 'if('.implode('&&', $vars).')';
								foreach ($vars as $ii => $var) {
									$line .= $delimiters[$ii].$var;
								}
								$content .= $line;
							}
							break;
						}
					}
				} else {
					$content .= $part;
				}
			}
		}
	}

	private static function parseDialogShortcuts(&$content) {
		$content = preg_replace('/\+\+> *(\w+) *(\((.*)\))* */', "Dialoger.show($1,$3)", $content);
		$content = preg_replace('/<\+\+ *(\w+) *(\((.*)\))* */', "Dialoger.hide($1,$3)", $content);
		$content = preg_replace('/\+> *(\w+) *(\((.*)\))*/', " Dialoger.get($1,$3)", $content);
	}

	private static function parseDispatchEventShortcuts(&$content) {
		$content = preg_replace('/--> *(\w+) *(\((.*)\))* *;*/', "this.dispatchEvent('$1',$3);", $content);
		$content = preg_replace('/==> *(\w+) *(\((.*)\))* *;*/', "Globals.dispatchEvent('$1',$3);", $content);
	}

	private static function parseTagShortcuts(&$content) {
		$content = preg_replace('/<::(\w+)> *<>/', "<::$1>.getElement()", $content);
		$content = str_replace('<>', ' this.getElement()', $content);
		$regexp = '/[\w\]\[\.]*<[\.\#:]*[a-z][\w\-\.\#\]\[]*>/i';
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
				$tag = preg_replace('/[^\.\#:\-\w]/', '', $tag);
				preg_match_all('/([\.\#:]*)([\w\-\.\#]+)/', $tag, $ms);
				if ($ms[1][0] == ':') {
					$content .= " this.getElement('".$ms[2][0]."')";
				} elseif ($ms[1][0] == '::') {
					$content .= " this.getChild('".$ms[2][0]."')";
				} else {
					$selector = !empty($ms[1][0]) ? $ms[1][0].'->>' : '';
					if ($index === null) {
						$content .= " this.findElement('".$selector.$ms[2][0].$scope."')";
					} elseif(empty($index)) {
						$content .= " this.findElements('".$selector.$ms[2][0].$scope."')";
					} else {
						$content .= " this.findElements('".$selector.$ms[2][0].$scope."')[".$index."]";
					}
				}
			}
		}
	}

	private static function parseDelayOperators(&$content) {
		$data = Splitter::split('/\bdelay *\( *(\d+) *\) *\{/', $content, 1);
		if (is_array($data)) {
			$content = '';
			for ($i = 0; $i < count($data['items']); $i++) {
				$content .= $data['items'][$i];
				if (isset($data['delimiters'][$i])) {
					$d = Splitter::getInner($data['items'][$i + 1]);
					$line = 'this.delay(function(){'.$d['inner'].'},'.$data['delimiters'][$i].');';
					$content .= $line;
					$data['items'][$i + 1] = $d['outer'];
				}
			}
		}
	}

	private static function prepareCode(&$content) {
		$content = preg_replace("/\t/", " ", $content);
		$content = preg_replace("/\n +/", "\n", $content);
	}

	private static function cleanCode(&$content) {
		$content = str_replace(",)", ")", $content);
		$content = str_replace("= ", "=", $content);
	}

}