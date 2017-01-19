<?php

	// priority 1
	// code before : delay (1000) { ... }
	// code after  : this.delay(function() { ... });
	
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