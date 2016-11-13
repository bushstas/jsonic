<?php

class JSInterpreter 
{	
	private static $className;

	public static function parse(&$content, $className) {
		self::$className = $className;
		self::prepareCode($content);
		self::parseDataConstants($content);
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
	
	public static function parseFunction(&$content, $className) {
		self::parseEachOperators($content);
	}

	private static function parseDataConstants(&$content) {
		$content = preg_replace('/\#([a-z]\w*)/i', "_DATA_#$1", $content);
	}

	private static function parseEachOperators(&$content) {
		$itemsName = '_items';
		$names = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','_');		
		$regexp = '/\beach\s*\(([\w\.\[\]]+) +as +(\w+)\)\s*\{/';
		$data = Splitter::split($regexp, $content);
		if (empty($data['items'])) return;
		$properNames = array();

		foreach ($names as $name) {
			if (!preg_match('/\b'.$name.'\b/', $content)) {
				$properNames[] = $name;
				if (count($properNames) >= count($data['delimiters'])) {
					break;
				}
			}
		}
		$content = '';
		foreach ($data['items'] as $i => $item) {
			$content .= $item;
			if (isset($data['delimiters'][$i])) {
				$v = $properNames[$i];
				$idx = $i + 1;
				if ($idx == 1) $idx = '';
				$d = $data['delimiters'][$i];
				$content .= preg_replace($regexp, "var ".$v."=$1,idx".$idx.";for(idx".$idx."=0;idx".$idx."<".$v.".length;idx".$idx."++){var $2=".$v."[idx".$idx."];", $d);
			}
		}		
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
		$content = preg_replace('/===> *(\w+) *(\((.*)\))* *;*/', "GlobalState.dispatchEvent('$1',$3);", $content);
		$content = preg_replace('/==> *(\w+) *(\((.*)\))* *;*/', "LocalState.dispatchEvent('$1',$3);", $content);
	}

	private static function parseTagShortcuts(&$content) {
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
				if ($ms[1][0] == ':') {
					$content .= " this.getElement('".$ms[2][0]."')";
				} elseif ($ms[1][0] == '::') {
					$content .= " this.getChild('".$ms[2][0]."')";
				} else {					
					if ($ms[2][0][0] == '@') {
						if ($ms[2][0] != '@') { 
							$ms[2][0] = self::getTagClassName().'_'.ltrim($ms[2][0], '@');
						} else {
							$ms[2][0] = self::getTagClassName();
						}
					}
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

	private static function getTagClassName() {
		$className = self::$className;
		$data = Splitter::split('/[A-Z]/', $className);
		$className = '';
		foreach ($data['items'] as $i => $item) {
			$className .= $item.'-';
			if (isset($data['delimiters'][$i])) {
				$className .= strtolower($data['delimiters'][$i]);
			}
		}
		return trim($className, '-');
	}

	private static function parseDelayOperators(&$content) {
		$regexp = '/\bdelay *\( *(\d+) *\) *\{/';
		$parts = preg_split($regexp, $content);
		$count = count($parts) - 1;
		if ($count > 0) {
			for ($j = 0; $j < $count; $j++) {
				$data = Splitter::splitOne($regexp, $content, 1);
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