<?php
	
class FileManager
{
	public static function createFile($path, $content) {
		$parts = explode('/', $path);
		if (count($parts) > 1) {
			$parts[count($parts) - 1] = '';
			$pathToFolder = implode('/', $parts);
			if (!is_dir($pathToFolder)) {
				self::createDir($pathToFolder);
			}
		}
		file_put_contents($path, $content);
	}

	public static function createDir($path) {
		$parts = explode('/', trim($path));
		$path = array();
		foreach ($parts as $part) {
			$path[] = $part;
			if (!empty($part) && $part != '.' && $parts != '..') {
				$currentPath = implode('/', $path);
				if (!is_dir($currentPath)) {
					mkdir($currentPath);
				}
			}
		}
	}

	public static function emptyFolder($dir) {
		if (is_dir($dir)) {
			$fs = scandir($dir);
			if (is_array($fs)) {
				foreach ($fs as $file) {
					if ($file != '..' && $file != '.') {
						$path = $dir."/".$file;
						if (is_dir($path)) {
							self::emptyFolder($path);
							rmdir($path);
						} elseif (file_exists($path)) {
							unlink($path);
						}
					}
				}
			}
		}
	}

	public static function getDirContent($dir, $extensions = null, $onlyFolders = false) {
		if (is_dir($dir)) {
			$items = scandir($dir);
			$properItems = array();
			foreach ($items as $item) {
				if ($item != '.' && $item != '..') {
					$properItems[] = $item;
				}
			}
			return $properItems;
		}
		return array();
	}
}

?>