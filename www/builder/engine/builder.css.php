<?php

class CSSCompiler 
{
	private $configProvider, $config;
	private $imagesFolderDefined = false;
	private $hasCssConstFiles = false;
	private $cssConstants = array();
	private $cssClassIndex = array();

	private $numericShortcuts = array(
		'l' => 'left', 'r' => 'right', 't' => 'top', 'b' => 'bottom', 'w' => 'width', 'h' => 'height', 'z' => 'z-index',
		'p' => 'padding', 'pl' => 'padding-left', 'pr' => 'padding-right', 'pt' => 'padding-top', 'pb' => 'padding-bottom',
		'm' => 'margin', 'ml' => 'margin-left', 'mr' => 'margin-right', 'mt' => 'margin-top', 'mb' => 'margin-bottom',
		'fs' => 'font-size', 'lh' => 'line-height', 'br' => 'border-radius', 'mah' => 'max-height', 'mih' => 'min-height',
		'maw' => 'max-width', 'miw' => 'min-width', 'bp' => 'background-position'
	);

	private $colorShortcuts = array(
		'c' => 'color', 'bc' => 'background-color', 'boc' => 'border-color'
	);

	private $borderShortcuts = array(
		'bo' => 'border', 'bol' => 'border-left', 'bot' => 'border-top', 'bor' => 'border-right', 'bob' => 'border-bottom'
	);

	private $errors = array(
		'folderIsNotString' => '�������� ��������� ������������ <b>cssFolder</b> �� �������� �������',
		'imagesFolderIsNotString' => '�������� ��������� ������������ <b>imagesFolder</b> �� �������� �������',
		'imagesFolderNotFound' => '����������, ��������� � ��������� ������������ <b>imagesFolder</b>, �� �������',
		'imagesFolderIsNotDefined' => '���������� ������������ css ���������� <b>imgsrc</b> ��� ����������� ��������� ������������ <b>imagesFolder</b>',
		'folderNameIsInvalid' => '�������� ��������� ������������ <b>cssFolder</b> �������� ����������� ������� {??}',
		'imagesFolderNameIsInvalid' => '�������� ��������� ������������ <b>imagesFolder</b> �������� ����������� ������� {??}',
		'cssConstDouble' => '���������� ������������� css ��������� {??} � ������� ���������� � ������ {??} � {??}',
		'cssConstDouble2' => '���������� ������������� css ��������� {??} � ������� ���������� � ����� {??}',
		'variableParse' => '������ ��� �������� CSS ����� {??}. ���������� {??} �������� �������������� ���������� {??}, ������� ������ ���� ���������� ����',
		'noCssConstFiles' => '���������� ������������� css ��������, �� �� ������ �� ���� ���� <b>.cssconst</b> ��� �� ��������<br>���������� ���� � ����� ������ � ������ ����������� � ����� ���������� ������ ����������<br><br><b>���������� ����� ������ ����� ���:</b><br><br>$white: #FFFFFF<br>$block: display: block;<br>$area: position: relative; margin: auto; background-color: #fff;<br><br><b>�������������:</b><br><br>.selector {<br>&nbsp;&nbsp;&nbsp;&nbsp;color: $white;<br>&nbsp;&nbsp;&nbsp;&nbsp;$block<br>&nbsp;&nbsp;&nbsp;&nbsp;$area<br>}',
		'noCssConst' => '���������� ����������� css ��������� {??}'
	);

	public function __construct($configProvider) {
		$this->configProvider = $configProvider;
	}

	private function init() {
		$this->config = $this->configProvider->getCssConfig();

		if (!is_string($this->config['folder'])) {
			new Error($this->errors['folderIsNotString']);
		}
		if (isset($this->config['images']) && !is_string($this->config['images'])) {
			new Error($this->errors['imagesFolderIsNotString']);
		}
		$this->validateFolderName($this->config['folder'], 'folderNameIsInvalid');
		$this->validateFolderName($this->config['images'], 'imagesFolderNameIsInvalid');

		if (!empty($this->config['images'])) {
			if (!is_dir(DEFAULT_PATH.$this->config['images'])) {
				new Error($this->errors['imagesFolderNotFound']);
			}
			$this->imagesFolderDefined = true;
		}
	}

	private function validateFolderName($folderName, $errorName) {
		preg_match_all('/([^\w\-])/', $folderName, $matches);
		if (!empty($matches[0])) {
			$symbols = array();
			foreach ($matches[1] as $s) {
				if (!in_array($s, $symbols)) {
					$symbols[] = $s;
				}
			}
			new Error($this->errors[$errorName], array('&laquo;'.implode('&raquo;, &laquo;', $symbols).'&raquo;'));
		}
	}

	public function getCssClassIndex() {
		return $this->cssClassIndex;
	}

	public function run($cssFiles, $cssConstFiles) {
		if (is_array($cssFiles) && !empty($cssFiles)) {
			$this->init();
			$this->initCssConstants($cssConstFiles);
			$content = array();
			foreach ($cssFiles as $file) {
				$css = '/* '.$file['name']." */\n".preg_replace('/\/\*[^\*]*\*\//', '', $file['content']);
				$this->parseVariables($css, $file['name']);
				$this->parseBackgroundImages($css, $file['name']);
				$content[] = $css;
			}
			$css = implode("\n", $content);
			$this->parseShadowShortcuts($css);
			
			$this->parseShortcutSets($css);
			$this->parseGradientShortcuts($css);
			$css = str_replace('<obr>', '(', $css);
			$css = str_replace('<cbr>', ')', $css);
		 	$css = str_replace('<sp>', ' ', $css);

			$this->parseNumericShortcuts($css);
			$this->parseColorShortcuts($css);
			$this->parseBorderShortcuts($css);
			$this->parseOtherShortcuts($css);

			$css = str_replace('px%', '%', $css);
			$css = str_replace('%px', '%', $css);
			$css = preg_replace('/\s0(px|%)/', " 0", $css);
			$css = preg_replace('/:0(px|%)/', ":0", $css);
			$css = preg_replace('/;{2,}/', ';', $css);
			$css = preg_replace('/==\w+/', '', $css);
			$css = preg_replace('/ {2,}/', ' ', $css);
			
			$this->parseCustomShortcuts($css);

			if ($this->configProvider->needCssObfuscation()) {
				$this->obfuscate($css);
			}
			$css = preg_replace("/ *> */", ">", $css);
			$css = preg_replace("/\t/", " ", $css);
			$css = preg_replace("/[\r\n]/", "", $css);
			$css = preg_replace("/\}/", "}\n", $css);
			$css = preg_replace("/ {1,}\{/", "{", $css);
			$css = preg_replace("/([:\{;,]) {1,}/", "$1", $css);
			$css = preg_replace("/\*\/[ \t]*([^\r\n])/", "*/\n$1", $css);
			$css = str_replace(";}", "}", $css);
			Gatherer::createFile(DEFAULT_PATH.$this->config['path'].'.css', $css);
		}
	}

	private function parseVariables(&$css, $filename) {
		$keys = array();
		preg_match_all('/([~\.\#a-z\- \*]+) *==(\w+)/i', $css, $matches);
		for ($i = 0; $i < count($matches[1]); $i++) {
			if (preg_match_all('/~(\w+)/', $matches[1][$i], $ms)) {
				foreach ($ms[1] as $m) {
					if (isset($keys[$m])) {
						$matches[1][$i] = str_replace('~'.$m, trim($keys[$m]), $matches[1][$i]);
					} else {
						new Error($this->errors['variableParse'], array($filename, $matches[2][$i], $m));
					}
				}
			}
			$keys[$matches[2][$i]] = $matches[1][$i];
		}		
		foreach ($keys as $k => $v) {
			$css = str_replace('~'.$k, trim($v), $css);
		}
	}

	private function parseBackgroundImages(&$css, $filename) {
		$regexp = '/\$imgsrc\s*=\s*([^\s]+)/';
		preg_match_all($regexp, $css, $matches);
		$pathsToImages = array();
		if (count($matches[1]) > 0) {
			if (!$this->imagesFolderDefined) {
				new Error($this->errors['imagesFolderIsNotDefined']);
			}
			for ($i = 0; $i < count($matches[1]); $i++) {
				$pathsToImages[] = $matches[1][$i];
				$css = preg_replace($regexp, '', $css);
				$css = preg_replace('/\$*(png|jpg|jpeg|gif)(\d*)\s*=\s*([^\s\)]+)/i', "background-image:url<obr><pathtoimg$2>$3.$1<cbr>;", $css);
			}
			$len = count($pathsToImages);
			$this->makeProperPathsToImages($pathsToImages);
			for ($i = 0; $i < $len; $i++) {
				$idx = $i == 0 ? '' : $i + 1;
				$css = str_replace('<pathtoimg'.$idx.'>', $pathsToImages[$i], $css);	
			}			
		}
	}

	private function makeProperPathsToImages(&$pathsToImages) {
		foreach ($pathsToImages as &$path) {
			$path = preg_replace('/^\.*\/'.$this->config['images'].'/', '', $path);
			$path = '../'.$this->config['images'].'/'.trim(trim($path, '.'), '/').'/';
		}
	}

	private function parseShadowShortcuts(&$css) {
		$css = preg_replace('/\$*bsh_(\d+)_(\d+)_(\d+)_\#(\w{3,6})/', "box-shadow:$1px<sp>$2px<sp>$3px<sp>#$4;", $css);
		$css = preg_replace('/\$*tsh_(\d+)_(\d+)_(\d+)_\#(\w{3,6})/', "text-shadow:$1px<sp>$2px<sp>$3px<sp>#$4;", $css);
	}

	private function parseGradientShortcuts(&$css) {
		$css = preg_replace('/\$*gr_(left|right|top|bottom)_(\#\w{3,6}|transparent)_(\#\w{3,6}|transparent)/', "background-image:linear-gradient(to<sp>$1,$2,$3<cbr>;", $css);
	}	

	private function parseShortcutSets(&$css) {
		$regexp = '/\$\s*\(([^\)]+)\)/';
		preg_match_all($regexp, $css, $matches);
		$matches = $matches[1];
		$parts = preg_split($regexp, $css);
		$css = '';
		foreach ($parts as $i => $part) {
			$css .= $part;
			if (isset($matches[$i])) {
				$styles = preg_split('/[ \$]/', $matches[$i]);
				foreach ($styles as $style) {
					if (!empty($style)) {
						if (preg_match('/^(box|text)-shadow/', $style)) {
							$css .= $style;
						} elseif (preg_match('/^background-image/', $style)) {
							$css .= $style;
						} else {
							$css .= '$'.trim($style, '$').' ';
						}
					}
				}
			}
		}
	}

	private function parseNumericShortcuts(&$css) {
		foreach ($this->numericShortcuts as $k => $v) {
			$regexp = '/\$'.$k.' *(-*\#*[\d\._\%]+)(%)*/';
			$px = !in_array($k, array('z')) ? 'px' : '';
			$css = preg_replace($regexp, $v.":$1".$px."$2;", $css);
			$css = preg_replace('/([:\s])(\d+%*)_(?=\d)/', "$1$2px ", $css);
		}	
	}
	
	private function parseColorShortcuts(&$css) {
		foreach ($this->colorShortcuts as $k => $v) {
			$regexp = '/\$'.$k.'\# *(\w{3,6})/';
			$css = preg_replace($regexp, $v.":#$1;", $css);
		}	
	}

	private function parseBorderShortcuts(&$css) {
		foreach ($this->borderShortcuts as $k => $v) {
			$regexp = '/\$'.$k.'\# *(\w{3,6})(_\d+)*/';
			$css = preg_replace($regexp, $v.":$2px solid #$1;", $css);
		}
		$css = str_replace(':px solid', ':1px solid', $css);
		$css = preg_replace('/:_(\d+)px solid/', ":$1px solid", $css);
	}

	private function parseOtherShortcuts(&$css) {
		$css = preg_replace('/\$rot(-*\d+)/', "transform:rotate($1deg);", $css);
		$css = preg_replace('/\$wh(\d+)(%)*/', "width:$1px$2;height:$1px$2;", $css);
	}

	private function parseCustomShortcuts(&$css) {
		$regexp = '/\$\w+/';
		preg_match_all($regexp, $css, $matches);
		$matches = $matches[0];
		if (!empty($matches) && !$this->hasCssConstFiles) {
			new Error($this->errors['noCssConstFiles']);
		}
		$parts = preg_split($regexp, $css);
		$css = '';
		foreach ($parts as $i => $part) {
			$css .= $part;
			if (isset($matches[$i])) {
				if (!isset($this->cssConstants[trim($matches[$i], '$')])) {
					new Error($this->errors['noCssConst'], array($matches[$i]));
				}
				$css .= $this->cssConstants[trim($matches[$i], '$')];
			}
		}
	}

	private function initCssConstants($files) {
		if (is_array($files)) {
			$this->hasCssConstFiles = !empty($files);
			$regexp = '/\$(\w+)\s*:\s*/';
			$fileNames = array();
			foreach ($files as $file) {
				preg_match_all($regexp, $file['content'], $matches);
				$varNames = $matches[1];
				if (!empty($varNames)) {
					$parts = preg_split($regexp, $file['content']);
					array_shift($parts);
					foreach ($parts as $i => $part) {
						$part = trim($part);
						if (isset($this->cssConstants[$varNames[$i]]) && $this->cssConstants[$varNames[$i]] != $part) {
							if ($file['filename'] != $fileNames[$varNames[$i]]) {
								new Error($this->errors['cssConstDouble'], array($varNames[$i], $fileNames[$varNames[$i]], $file['filename']));
							} else {
								new Error($this->errors['cssConstDouble2'], array($varNames[$i], $file['filename']));
							}
						}
						$this->cssConstants[$varNames[$i]] = $part;
						$fileNames[$varNames[$i]] = $file['filename'];
					}
				}
			}
		}
	}

	private function obfuscate(&$css) {
		$regexp = '/url\([^\)]+\)/';
		preg_match_all($regexp, $css, $matches);
		$urls = $matches[0];
		$css = preg_replace($regexp, '__URL__', $css);

		preg_match_all('/\.([a-z][\w\-]{3,})/i', $css, $matches);
		$classes = array_unique($matches[1]);
		foreach ($classes as $class) {
			$this->cssClassIndex[$class] = generateObfiscatedCssClassName();
		}
		foreach ($this->cssClassIndex as $k => $v) {
			$css = preg_replace('/\.'.$k.'([\s\.\#,\{:\)])/', '.'.$v."$1", $css);
		}
		$parts = explode('__URL__', $css);
		$css = '';
		foreach ($parts as $i => $part) {
			$css .= $part;
			if (isset($urls[$i])) {
				$css .= $urls[$i];
			}
		}
	}
}