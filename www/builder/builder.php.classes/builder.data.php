<?php

class DataCompiler 
{	
	private $dataIndex = array();
	private $dataConstants = array();
	private $nameFiles = array();
	private $regexp = '\#(\w+)\s*=\s*';

	private $errors = array(
		'duplicateInFile' => 'Дублирование контстанты данных {??} в файлe {??}.data',
		'duplicateInFiles' => 'Дублирование контстанты данных {??} в файлах {??}.data и {??}.data',
		'parseError' => 'Ошибка парсинга контстанты данных в файле {??}.data<br><br>{?}'
	);

	public function run($dataFiles) {
		if (!empty($dataFiles)) {
			foreach ($dataFiles as $dataFile) {
				$data = $dataFile['content'];
				$file = $dataFile['name'];
				preg_match_all('/'.$this->regexp.'/', $data, $matches);
				$vars = $matches[1];
				foreach ($vars as $varName) {
					if (isset($this->nameFiles[$varName])) {
						if ($this->nameFiles[$varName] == $file) {
							new Error($this->errors['duplicateInFile'], array($varName, $this->nameFiles[$varName]));
						} else {
							new Error($this->errors['duplicateInFiles'], array($varName, $this->nameFiles[$varName], $file));
						}
					}
					$this->nameFiles[$varName] = $file;
					$this->dataIndex[] = $varName;
				}
				$data = '{'.trim(preg_replace('/;*\s*'.$this->regexp.'/', ",'$1':", $data), ',').'}';
				TextParser::transformIntoValidJson($data, true);
				$data = preg_replace('/@(\w+)/', "<nq>__.$1<nq>", $data);
				$strData = preg_replace('/(CONFIG\.\w+\.\w+)/', "<nq>$1<nq>", $data);
				$data = json_decode($strData, true);
				if ($data === null) {
					new Error($this->errors['parseError'], array($file, $strData));
				}
				$this->dataConstants[] = $strData;
			}
		}
	}

	public function get() {
		return array(
			'index' => $this->dataIndex,
			'data' => $this->dataConstants,
			'files' => $this->nameFiles
		);
	}
}