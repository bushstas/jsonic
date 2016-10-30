<?php 

class Splitter
{
	public static function split($regexp, $text, $matchesIndex = 0) {
		preg_match_all($regexp, $text, $matches);
		if (empty($matches[0])) return;
		$regexp = '/'.trim(trim($regexp, '#'), '/').'/';
		$items = preg_split($regexp, $text);
		return array(
			'items' => $items,
			'delimiters' => $matchesIndex === 'all' ? $matches : $matches[$matchesIndex]
		);
	}

	public static function join($items, $delimiters) {
		$text = '';
		for ($i = 0; $i < count($items); $i++) {
			$text .= $items[$i];
			if (isset($delimiters[$i])) {
				$text .= $delimiters[$i];
			}
		}
		return $text;
	}

	public static function getInner($text, $closing = '}', $opening = '{') {
		$inner = '';
		$outer = '';
		$closed = true;
		$parts = explode($closing, $text);
		if (isset($parts[1])) {
			$count = 0;
			$added = 0;
			foreach ($parts as &$part) {
				$ps = explode($opening, $part);
				if (!isset($ps[1])) {
					$inner .= $part;
				} else {
					$inner .= implode($opening, $ps);
					$count += count($ps) - 1;
					$added += count($ps) - 1;
				}
				if ($count == 0) {
					$added++;
					$p = array();
					for ($i = $added; $i < count($parts); $i++) {
						$p[] = $parts[$i];
					}
					$outer = implode($closing, $p);
					break;
				}
				$inner .= $closing;
				$count--;
			}

		} else {
			$inner = $text;
			$closed = false;
		}
		return array(
			'inner' => $inner,
			'outer' => $outer,
			'closed' => $closed
		);
	}
}