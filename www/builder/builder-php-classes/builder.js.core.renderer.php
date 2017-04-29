<?php

class JSCoreRenderer
{
	private $dir = 'jscore';
	private $outputDir = 'core';
	private $classes = array();
	
	public function run() {
		$this->dir = FOLDER.'/'.$this->dir;
		$this->outputDir = FOLDER.'/../'.$this->outputDir;
		if (is_dir($this->outputDir)) {
			FileManager::emptyFolder($this->outputDir);
		} else {
			mkdir($this->outputDir);
		}
		$this->handleDir($this->dir, $this->outputDir);
	}

	private function handleDir($dir, $outputDir) {
		$items = FileManager::getDirContent($dir, array('php'));
		foreach ($items as $item) {
			$path = $dir.'/'.$item;
			$outputPath = $outputDir.'/'.$item;
			if (is_dir($path)) {
				if (!is_dir($outputPath)) {
					mkdir($outputPath);
				}
				$this->handleDir($path, $outputPath);
			} elseif (file_exists($path)) {
				$outputPath = $this->correctOutputFileName($outputPath);
				$this->handleFile($path, $outputPath);
			}
		}
	}

	private function handleFile($path, $outputPath) {
		include $path;
		if (is_array($data)) {
			extract($data);
		}
		if (empty($name) && empty($prototypeOf) && empty($functions)) return;

		
		if (!empty($name) || !empty($prototypeOf)) {
			$this->classes[!empty($name) ? $name : $prototypeOf] = array();
			$class = &$this->classes[!empty($name) ? $name : $prototypeOf];
			$class['overridableMethods'] = $overridableMethods;
			$class['templateCallableMethods'] = $templateCallableMethods;
		}
		
		$content = array();
		if (!empty($beforeCondition)) {
			$content[] = $this->correctContent($beforeCondition);
		}
		if (!empty($condition)) {
			$content[] = 'if('.$condition.'){';
		}
		if (!empty($before)) {
			$content[] = $this->correctContent($before);
		}
		if (is_array($functions) && !empty($functions)) {
			foreach ($functions as $methodName => $methodData) {
				$this->addMethod($methodName, $methodData, $content, 4);
			}
		}
		if (is_array($privateMethods) && !empty($privateMethods)) {
			$class['privateMethods'] = array_keys($privateMethods);
			foreach ($privateMethods as $methodName => $methodData) {
				$this->addMethod($methodName, $methodData, $content, 1);
			}
		}
		if (is_array($thisMethods) && !empty($thisMethods)) {
			$class['thisMethods'] = array_keys($thisMethods);
			foreach ($thisMethods as $methodName => $methodData) {
				$this->addMethod($methodName, $methodData, $content, 2);
			}
		}
		if (is_array($methods) && !empty($methods)) {
			$class['methods'] = array_keys($methods);
			if (empty($prototypeOf)) {
				$prototypeOf = CONST_COMPONENT;
			}
			$content[] = CONST_PROTO.'='.$prototypeOf.'.prototype;';
			foreach ($methods as $methodName => $methodData) {
				$this->addMethod($methodName, $methodData, $content, 3);
			}
		}
		if (empty($mode) || $mode == 1) {
			$content[] = 'return '.CONST_COMPONENT.';';
		}
		if (!empty($after)) {
			$content[] = $this->correctContent($after);
		}
		if (!empty($condition)) {
			$content[] = '}';
		}
		if (!empty($afterCondition)) {
			$content[] = $this->correctContent($afterCondition);
		}
		$content = implode("\n", $content);
		list($topContent, $bottomContent) = $this->getTopAndBottomContent($mode, $name, $args, $var);
		$fileContent = $this->correctFinalContent($topContent."\n".$content."\n".$bottomContent);
		if (!empty($define) && !empty($var)) {
			$fileContent = 'var '.$var.';'.$fileContent;
		}
		FileManager::createFile($outputPath, $fileContent);
	}

	private function addMethod($methodName, $data, &$content, $type) {
		if (!empty($data['before'])) {
			$content[] = $data['before'];
		}
		if (!empty($data['value'])) {
			$value = $this->correctContent($data['value']);
			if ($type == 1) {
				$content[] = 'var '.$methodName.'='.$value.';';
			} elseif ($type == 2) {
				$content[] = 'this.'.$methodName.'='.$value.';';
			} elseif ($type == 3) {
				$content[] = CONST_PROTO.'.'.$methodName.'='.$value.';';
			}
		} else {
			$args = $this->getArgs($data['args']);
			if ($type == 1) {
				$function = 'var '.$methodName.'=function('.$args.'){';
			} elseif ($type == 2) {
				$function = 'this.'.$methodName.'=function('.$args.'){';
			} elseif ($type == 3) {
				$function = CONST_PROTO.'.'.$methodName.'=function('.$args.'){';
			} else {
				$function = 'function '.$methodName.'('.$args.'){';
			}
			if (!empty($data['body'])) {
				$body = $this->correctContent($data['body']);
				$content[] = $function.$body.'};';
			} else {
				$content[] = $function.'};';
			}
		}
		if (!empty($data['after'])) {
			$content[] = $data['after'];
		}
	}

	private function getTopAndBottomContent($mode, $name, $args, $var) {
		switch ((int)$mode) {
			case 5:
				return array('','');

			case 4:
				return array(
					';(function(){',
					"})();"
				);
			case 3:
				return array(
					CONST_GLOBAL.'.set(function('.$this->getArgs($args).'){',
					"},'".$name."');"
				);
			case 2:
				return array(
					CONST_GLOBAL.'.set('.(!empty($var) ? $var.'=' : '').'new(function('.$this->getArgs($args).'){',
					"})(),'".$name."');"
				);
			default:
				return array(
					CONST_GLOBAL.'.set(('.CONST_COMPONENT.'=function('.$this->getArgs($args).'){',
					"})(),'".$name."');"
				);
		}
	}

	private function correctOutputFileName($outputPath) {
		return preg_replace('/\.php$/', '.js', $outputPath);
	}

	private function getArgs($args) {
		if (is_array($args)) {
			return implode(',', $args);
		}
		return '';
	}

	private function removeExtraSpaces($text) {
		$text = preg_replace('/([^\w])\s+/i', "$1", $text);
		$text = preg_replace('/\s+([^\w])/i', "$1", $text);
		return trim($text);
	}

	private function correctContent($text) {
		TextParser::encode($text, 'jscorerenderer');
		$text = $this->removeExtraSpaces($text);
		TextParser::decode($text, 'jscorerenderer');
		return $text;
	}

	private function correctFinalContent($text) {
		$text = preg_replace('/;(?=\})/i', '', $text);
		return trim($text);	
	}
}

?>