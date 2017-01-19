<?php

	// priority 2
	// code before : @(element).addClass('active');
	// code after  : if (element) element.addClass('active');
	
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