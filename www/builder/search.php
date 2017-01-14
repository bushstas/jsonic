<?php


include 'builder.php.classes/builder.printer.php';

$search = $_REQUEST['search'];
if (empty($search)) {
    die('Введите искомое слово');
}
$len = strlen($search) - 1;
if ($search[0] == '/' && $search[$len] == '/') {
    $isRegex = true;
    $search = trim($search, '/');
}

function getDirContents($dir, &$results = array()) {
    $files = scandir($dir);

    foreach($files as $key => $value){
        $path = $dir.'/'.$value;
        $realpath = realpath($dir.'/'.$value);
        if(!is_dir($path)) {
            $results[] = $path;
        } else if($value != "." && $value != "..") {
            getDirContents($path, $results);
        }
    }

    return $results;
}

$config = json_decode(file_get_contents('config.json'), true);
$scope = $config['scope'];
$tests = $config['tests'];
$scripts = $config['scripts'];

$files = array();
if (!empty($scope) && is_dir($scope)) {
    $files = getDirContents($scope);
}
if (!empty($tests) && is_dir($tests)) {
    $files = array_merge($files, getDirContents($tests));
}
if (!empty($scripts) && is_dir($scripts)) {
    $files = array_merge($files, getDirContents($scripts));
}


$found = array();
foreach ($files as $file) {
    $content = file_get_contents($file);
    if ($isRegex) {
        $parts = preg_split('/'.$search.'/', $content);
    } else {
        $parts = explode($search, $content);
    }
    if (count($parts) > 1) {
        $found[] = $file;
    }
}
if (empty($found)) {
    die('Ничего не найдено');
}
Printer::log($found, true);