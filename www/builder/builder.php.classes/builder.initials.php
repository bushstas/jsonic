<?php

class InitialsParser 
{	
	private $currentClass, $currentClassName, $currentObject, $currentBindings;
	private $errors = array(
		'incorrectInitial' => 'Ошибка в парсинге кода initial параметра в классе {??}<xmp>initial {?}</xmp>Описание должно иметь вид <b>initial props = {...}</b> или <b>initial controllers = [...]</b>',
		'unknownInitial' => 'Неизвестный initial параметр {??} в классе {??}',
		'parseError' => 'Ошибка в парсинге кода initial параметра {??} в классе {??}<xmp>initial {?} = {?}</xmp><b>Параметр должен иметь вид:</b> {?}',
		'notAssocArray' => 'Initial параметр {??} в классе {??} должен быть ассоциативным массивом (объектом).<xmp>initial {?} = {?}</xmp><b>Параметр должен иметь вид:</b> {?}',
		'notArray' => 'Initial параметр {??} в классе {??} должен быть простым массивом.<xmp>initial {?} = {?}</xmp><b>Параметр должен иметь вид:</b> {?}',
		'noPatternKey' => 'Initial параметр {??} в классе {??} имеет поле {??} не удовлетворяющее паттерну <b>^[a-z]\w*$</b><xmp>initial {?} = {?}</xmp>',
		'incorrectValue' => 'Initial параметр {??} в классе {??} имеет поле {??}, в котором параметр {??} имеет некорректное значение {??}<xmp>initial {?} = {?}</xmp><b>Параметр должен иметь вид:</b> {?}',
		'incorrectValue2' => 'Initial параметр {??} в классе {??} имеет поле {??} с некорректным значением {??}<xmp>initial {?} = {?}</xmp>Ожидается функция обработчик вида <b>this.{?}</b><br><br><b>Параметр должен иметь вид:</b> {?}',
		'incorrectBind' => 'Initial параметр {??} в классе {??} имеет поле {??}, в котором параметр {??} имеет некорректный первый аргумент {??} вызова bind для метода {??}<br><br>Ожидается <b>this</b> или <b>null</b><xmp>initial {?} = {?}</xmp><b>Параметр должен иметь вид:</b> {?}',
		'incorrectBind2' => 'Initial параметр {??} в классе {??} имеет поле {??}, в котором параметр {??} имеет некорректный аргумент {??} вызова bind  для метода {??}<br><br>Ожидается JSON валидная запись или текстовая контстанта вида <b>@shortcut</b><xmp>initial {?} = {?}</xmp><b>Параметр должен иметь вид:</b> {?}',
		'fieldNotAssocArray' => 'Initial параметр {??} в классе {??} имеет поле {??}, которое не является ассоциативным массивом<xmp>initial {?} = {?}</xmp><b>Параметр должен иметь вид:</b> {?}',
		'fieldEmpty' => 'Initial параметр {??} в классе {??} имеет поле {??}, которое является пустым<xmp>initial {?} = {?}</xmp><b>Параметр должен иметь вид:</b> {?}',
		'noUrl' => 'Initial параметр {??} в классе {??} имеет поле {??}, в котором отсутствует параметр <b>url</b><xmp>initial {?} = {?}</xmp><b>Параметр должен иметь вид:</b> {?}',
		'noController' => 'Initial параметр {??} в классе {??} не имеет поля <b>controller</b><xmp>initial {?} = {?}</xmp><b>Параметр должен иметь вид:</b> {?}',
		'incorrectAsync' => 'Initial параметр {??} в классе {??} имеет поле <b>async</b>, но его тип не <b>boolean</b><xmp>initial {?} = {?}</xmp><b>Параметр должен иметь вид:</b> {?}',
		'incorrectOptions' => 'Initial параметр {??} в классе {??} имеет поле <b>options</b>, но он не является ассоциативным массивом<xmp>initial {?} = {?}</xmp><b>Параметр должен иметь вид:</b> {?}',
		'itemNotAssocArray' => 'Initial параметр {??} в классе {??} имеет элемент с индексом {??}, который не является ассоциативным массивом<xmp>initial {?} = {?}</xmp><b>Параметр должен иметь вид:</b> {?}',
		'itemNoParam' => 'Initial параметр {??} в классе {??} имеет элемент с индексом {??}, у которого отсутствует параметр {??}><xmp>initial {?} = {?}</xmp><b>Параметр должен иметь вид:</b> {?}',
		'paramNotArray' => 'Initial параметр {??} в классе {??} имеет элемент с индексом {??}, у которого параметр {??} не является массивом<xmp>initial {?} = {?}</xmp><b>Параметр должен иметь вид:</b> {?}',
		'typeError' => 'Класс {??} с типом {??} не может содержать initial параметр {??}',
		'noActions' => 'У контроллера {??} отсутствуют initial параметр <b>actions</b>.<br><br><b>Параметр должен иметь вид:</b> {?}',
		'privateParamNotBool' => 'Initial параметр {??} в классе {??} имеет поле <b>private</b>, которое не является true или false<xmp>initial {?} = {?}</xmp><b>Параметр должен иметь вид:</b> {?}',
		'controllerMustBePrivate' => 'Initial параметр {??} в классе {??} имеет поле <b>options</b>, но его поле <b>private</b> отсутствует или не является true<br>Только работая с компонентом приватно, контрорллер может использовать параметр <b>options</b><xmp>initial {?} = {?}</xmp><b>Параметр должен иметь вид:</b> {?}'

	);

	private $componentLikeClassTypes = array('component', 'dialog', 'form', 'control', 'menu', 'view', 'application');
	private $availableInitials = array('loader', 'controllers', 'props', 'globals', 'actions', 'options', 'args', 'helpers', 'followers', 'correctors');
	private $initials = array();
	private $regexp = '/\binitial\s+([\s\S]+?)(?=(initial|function|@EOF))/';
	private $regexp2 = '/^([a-zA-Z]\w*)\s*=\s*([\s\S]+?)[;\s]*$/';
	private $actionsCache = array();

	public function fetch(&$content, &$class) {
		$this->defineForClass($class);
		$content .= '@EOF';
		preg_match_all($this->regexp, $content, $matches);
		$initials = $matches[1];
		foreach ($initials as $initial) {
			$initial = trim($initial);
			preg_match_all($this->regexp2, $initial, $matches);
			$initialType = trim($matches[1][0]);
			$initialValue = trim($matches[2][0]);
			if (empty($initialType) || empty($initialValue)) {
				new Error($this->errors['incorrectInitial'], array($this->currentClassName, $initial));
			}
			if (!in_array($initialType, $this->availableInitials)) {
				new Error($this->errors['unknownInitial'], array($initialType, $this->currentClassName));
			}
			$this->initials[$this->currentClassName][$initialType] = $initialValue;
		}
		$this->currentClass['initials'] = &$this->initials[$this->currentClassName];
		$content = preg_replace('/(\b)initial\s+(\w+\s*=\s*[\{\[][\s\S]+?)(?=(initial|function|@EOF))/', "$1", $content);
		$content = str_replace('@EOF', '', $content);
	}

	public function run(&$classes) {
		foreach ($this->initials as $className => &$initials) {
			if (!isset($classes[$className])) continue;
			$this->defineForClass($classes[$className]);
			foreach ($initials as $type => &$value) {
				$this->validateInitialValue($type, $value);
				if ($this->currentClass['type'] == 'controller') {
					$this->validateControllerInitials($this->currentClass['initials']);
				}
			}
		}
	}

	private function validateControllerInitials($initials) {
		if (empty($initials['actions'])) {
			new Error($this->errors['noActions'], array($this->currentClassName, $this->getInitialParamExample('actions')));
		}
	}

	private function defineForClass(&$class) {
		$this->currentClass = &$class;
		$this->currentClassName = $class['name'];
		if (!is_array($this->initials[$class['name']])) {
			$this->initials[$class['name']] = array();
		}
	}

	private function validateInitialValue($type, $value) {
		$this->parseInitialsObject($value, $type);
		switch ($type) {
			case 'args':
			case 'options':
			case 'props':
				$this->validateDefaultInitials($value, $type);
			break;

			case 'actions':
				$this->validateActionsInitials($value, $type);
			break;

			case 'loader':
				$this->validateLoaderInitials($value, $type);
			break;

			case 'globals':
				$this->validateGlobalsInitials($value, $type);
			break;

			case 'helpers':
				$this->validateHelpersInitials($value, $type);
			break;

			case 'followers':
				$this->validateFollowersInitials($value, $type);
			break;

			case 'controllers':
				$this->validateControllersInitials($value, $type);
			break;

			case 'correctors':
				$this->validateCorrectorsInitials($value, $type);
			break;
		}
	}

	public function get() {
		return $this->initials;
	}

	public function getControllerActions($ctr) {
		return $this->actionsCache[$ctr];
	}

	private	function parseInitialsObject($value, $type) {
		$this->currentObject = array();
		$this->currentBindings = array();
		$originalValue = $value;
		$value = trim(preg_replace('/[\r\n\t]/', '', $value));
		if ($value == '{}' || $value == '[]') {
			return;
		}
		$value = preg_replace('/:\s+/', ':', $value);
		$regexp = '/\.bind\(([^\)]+)\)/';		
		preg_match_all($regexp, $value, $binds);
		$this->currentBindings = $binds[1];
		
		$text = preg_replace($regexp, '__BIND__', $value);
		TextParser::transformIntoValidJson($text);
		
		$this->currentObject = json_decode($text, true);
		if ($this->currentObject === null) {
			new Error($this->errors['parseError'], array($type, $this->currentClassName, $type, $originalValue, $this->getInitialParamExample($type)));
		}
	}

	private	function getInitialParamExample($type) {
		switch ($type) {
			case 'actions':
				return "<xmp>initial actions = {\n\t'load': {\n\t\t'url': './path',\n\t\t'method': 'GET',\n\t\t'callback': this.onLoad\n\t}\n}</xmp><b>или</b><xmp>initial actions = {\n\t'load': {\n\t\t'url': './path',\n\t\t'method': 'POST',\n\t\t'callback': this.onLoad.bind(this, ...args)\n\t}\n}</xmp>";
			case 'loader':
				return "<xmp>initial loader = {\n\t'controller': ControllerClass,\n\t'async': true,\n\t'options': {\n\t\t'key': 'value'\n\t}\n}</xmp>";
			case 'options':
				return "<xmp>initial options = {\n\t'key': 'id',\n\t'store': true,\n\t'storeAs': 'items',\n\t'storePeriod': '1day',\n\t'clone': true\n}</xmp>";
			case 'args':
				return "<xmp>initial args = {\n\t'name': 'Name',\n\t'price': 1000,\n\t'tags': ['tag1', 'tag2']\n}</xmp>";
			case 'globals':
				return "<xmp>initial globals = {\n\t'userOnline': this.onChangeUserOnlineStatus,\n\t'siteBackground': this.onChangeSiteBackground.bind(this, true)\n}</xmp>";
			case 'props':
				return "<xmp>initial props = {\n\t'width': 100,\n\t'color': '#FFFFFF'\n}</xmp>";
			case 'helpers':
				return "<xmp>initial helpers = [\n\t{\n\t\t'helper': HelperClass,\n\t\t'callback': this.handleHelperClass,\n\t\t'options': {\n\t\t\t...\n\t\t}\n\t}\n]</xmp>";
			case 'followers':
				return "<xmp>initial followers = {\n\t'somePropName': this.onChangeSomeProp,\n\t'somePropName2': this.onChangeSomeProp2\n}</xmp>";
			case 'controllers':
				return "<xmp>initial controllers = [\n\t{\n\t\t'controller': ControllerClass,\n\t\t'on': {\n\t\t\t'load': this.onLoad,\n\t\t\t'add': this.onAdd\n\t\t},\n\t\t'options': {\n\t\t\t'load': {...},\n\t\t\t'add': {...}\n\t\t},\n\t\t'private': true\n\t}\n]</xmp>";
		
		}
	}

	private	function isAssocArray($obj, $value = '') {
		if (!is_array($obj) || $value[0] == '[') return false;
		if (empty($obj)) return true;
		$keys = array_keys($obj);
		return is_string($keys[0]);
	}

	private function isArray($arr, $value = '') {
		if (!is_array($arr) || $value[0] == '{') return false;
		if (empty($obj)) return true;
		$keys = array_keys($obj);
		return is_int($keys[0]);
	}

	private function initialAssocArrayTypeError($value, $type) {
		new Error($this->errors['notAssocArray'], array($type, $this->currentClassName, $type, $value, $this->getInitialParamExample($type)));
	}

	private function initialArrayTypeError($value, $type) {
		new Error($this->errors['notArray'], array($type, $this->currentClassName, $type, $value, $this->getInitialParamExample($type)));
	}

	private function initialError($type) {
		new Error($this->errors['typeError'], array($this->currentClassName, $this->currentClass['type'], $type));	
	}

	private function validateObjectFields($obj, $value, $type) {
		if (is_array($obj)) {
			foreach ($obj as $key => $val) {
				if (!preg_match('/^[a-z]\w*$/', $key)) {
					new Error($this->errors['noPatternKey'], array($type, $this->currentClassName, $key, $type, $value));
				}
				if ($this->isAssocArray($val)) {
					$this->validateObjectFields($val, $value, $type);
				}
			}
		}
	}

	private function validateCallback($callback, $value, $type, $field, $name = '', $val = '') {
		if (empty($callback)) return;
		if ($callback !== null && $callback !== false && $callback !== '') {
			$callback = str_replace('__BIND__', '', $callback);
			if (!preg_match('/^this\.[a-z]\w*$/i', $callback)) {
				if (!empty($name)) {
					new Error($this->errors['incorrectValue'], array($type, $this->currentClassName, $field, $name, $callback, $type, $value, $this->getInitialParamExample($type)));
				} else {
					new Error($this->errors['incorrectValue2'], array($type, $this->currentClassName, $field, $val, $callback, $type, $value, $type == 'globals' ? 'onChangeGlobalVar' : 'onChangeSomeProp', $this->getInitialParamExample($type)));
				}
			}
		}
		$binds = $this->currentBindings;
		if (is_array($binds)) {
			foreach ($binds as $bind) {
				$parts = preg_split('/\s*,\s*/', $bind);
				if ($parts[0] != 'this' && $parts[0] != 'null') {
					new Error($this->errors['incorrectBind'], array($type, $this->currentClassName, $field, $name, $bind, $callback, $type, $value, $this->getInitialParamExample($type)));
				}
			}
			for ($i = 1; $i < count($parts); $i++) {
				$part = removeQuotedText($parts[$i]);
				if (!preg_match('/^@\w+$/', $part) && json_decode("[".$part."]") === null) {
					new Error($this->errors['incorrectBind2'], array($type, $this->currentClassName, $field, $name, $parts[$i], $callback, $type, $value, $this->getInitialParamExample($type)));
				}
			}
		}
		$callback = str_replace('__BIND__', '', str_replace('this.', '', $callback));
		$this->callbacks[$this->currentClassName][] = $callback;
	}

	private function validateDefaultInitials($value, $type) {
		$initials = $this->currentObject;
		if (($type == 'args' || $type == 'props') && !in_array($this->currentClass['type'], $this->componentLikeClassTypes)) {
			$this->initialError($type);
		}
		if (!$this->isAssocArray($initials, $value)) {
			$this->initialAssocArrayTypeError($value, $type);
		}
		$this->validateObjectFields($initials, $value, $type);
	}

	private function validateActionsInitials($value, $type) {
		if (!is_array($this->actionsCache[$this->currentClassName])) {
			$this->actionsCache[$this->currentClassName] = array();
		}		
		$initials = $this->currentObject;
		if (!$this->isAssocArray($initials, $value)) {
			$this->initialAssocArrayTypeError($value, $type);
		}
		$this->validateObjectFields($initials, $value, $type);
		foreach ($initials as $key => $val) {
			$this->actionsCache[$this->currentClassName][] = $key;
			if (!$this->isAssocArray($val, $value)) {
				new Error($this->errors['fieldNotAssocArray'], array($type, $this->currentClassName, $key, $type, $value, $this->getInitialParamExample($type)));
			}
			if (empty($val)) {
				new Error($this->errors['fieldEmpty'], array($type, $this->currentClassName, $key, $type, $value, $this->getInitialParamExample($type)));
			}
			if (empty($val['url'])) {
				new Error($this->errors['noUrl'], array($type, $this->currentClassName, $key, $type, $value, $this->getInitialParamExample($type)));
			}
			$this->validateCallback($val['callback'], $value, $type, $key, 'callback');
		}
		$this->addActionsToClass($initials);
	}

	private function validateLoaderInitials($value, $type) {
		$initials = $this->currentObject;
		if (!in_array($this->currentClass['type'], $this->componentLikeClassTypes)) {
			$this->initialError('loader');
		}
		if (!$this->isAssocArray($initials, $value)) {
			$this->initialAssocArrayTypeError($value, $type);
		}
		$this->validateObjectFields($initials, $value, $type);
		if (empty($initials['controller'])) {
			new Error($this->errors['noController'], array($type, $this->currentClassName, $type, $value, $this->getInitialParamExample($type)));
		}
		if (isset($initials['async']) && !is_bool($initials['async'])) {
			new Error($this->errors['incorrectAsync'], array($type, $this->currentClassName, $type, $value, $this->getInitialParamExample($type)));
		}
		if (isset($initials['options']) && !$this->isAssocArray($initials['options'])) {
			new Error($this->errors['incorrectOptions'], array($type, $this->currentClassName, $type, $value, $this->getInitialParamExample($type)));
		}
		$this->addControllerToClass($initials['controller']);
	}

	private function validatePropsInitials($value, $type) {
		$initials = $this->currentObject;
		if (!in_array($this->currentClass['type'], $this->componentLikeClassTypes)) {
			$this->initialError('props');
		}
		if (!$this->isAssocArray($initials, $value)) {
			$this->initialAssocArrayTypeError($value, $type);
		}
		$this->validateObjectFields($initials, $value, $type);
	}

	private function validateGlobalsInitials($value, $type) {
		$initials = $this->currentObject;
		if (!$this->isAssocArray($initials, $value)) {
			$this->initialAssocArrayTypeError($value, $type);
		}
		$this->validateObjectFields($initials, $value, $type);
		foreach ($initials as $key => $val) {
			$this->validateCallback($val, $value, $type, $key, '', $val);
		}
	}

	private function validateCorrectorsInitials($value, $type) {
		$initials = $this->currentObject;
		if (!$this->isAssocArray($initials, $value)) {
			$this->initialAssocArrayTypeError($value, $type);
		}
		$this->validateObjectFields($initials, $value, $type);
		foreach ($initials as $key => $val) {
			$this->validateCallback($val, $value, $type, $key, '', $val);
		}
	}

	private function validateHelpersInitials($value, $type) {
		$initials = $this->currentObject;
		if (!in_array($this->currentClass['type'], $this->componentLikeClassTypes)) {
			$this->initialError('helpers');
		}
		if (!$this->isArray($initials, $value)) {
			$this->initialArrayTypeError($value, $type);
		}
		foreach ($initials as $i => $val) {
			if (!$this->isAssocArray($val)) {
				new Error($this->errors['itemNotAssocArray'], array($type, $this->currentClassName, $i, $type, $value, $this->getInitialParamExample($type)));
			}
			if (empty($val['helper'])) {
				new Error($this->errors['itemNoParam'], array($type, $this->currentClassName, $i, 'helper', $type, $value, $this->getInitialParamExample($type)));
			}
			$this->validateCallback($val['callback'], $value, $type, $i, 'callback');
		}
	}

	private function validateFollowersInitials($value, $type) {
		$initials = $this->currentObject;
		if (!in_array($this->currentClass['type'], $this->componentLikeClassTypes)) {
			$this->initialError('followers');
		}
		if (!$this->isAssocArray($initials, $value)) {
			$this->initialAssocArrayTypeError($value, $type);
		}
		$this->validateObjectFields($initials, $value, $type);
		foreach ($initials as $key => $val) {
			$this->validateCallback($val, $value, $type, $key, '', $val);
		}
	}

	private function validateControllersInitials($value, $type) {
		$initials = $this->currentObject;
		if (!$this->isArray($initials, $value)) {
			$this->initialArrayTypeError($value, $type);
		}
		$this->currentClass['onActions'] = array();
		foreach ($initials as $i => $val) {
			$this->validateObjectFields($val, $value, $type);
			if (!$this->isAssocArray($val)) {
				new Error($this->errors['itemNotAssocArray'], array($type, $this->currentClassName, $i, $type, $value, $this->getInitialParamExample($type)));

			}
			if (empty($val['controller'])) {
				new Error($this->errors['itemNoParam'], array($type, $this->currentClassName, $i, 'controller', $type, $value, $this->getInitialParamExample($type)));
			}
			if (isset($val['on'])) {
				if (!is_array($val['on'])) {
					new Error($this->errors['paramNotArray'], array($type, $this->currentClassName, $i, 'on', $type, $value, $this->getInitialParamExample($type)));
				}
				$this->validateObjectFields($val['on'], $value, $type);
				foreach ($val['on'] as $action => $callback) {
					$this->validateCallback($callback, $value, $type, $i, 'callback');
					$this->currentClass['onActions'][] = array('controller' => $val['controller'], 'action' => $action);
				}
			}
			if (isset($val['private']) && !is_bool($val['private'])) {
				new Error($this->errors['privateParamNotBool'], array($type, $this->currentClassName, $type, $value, $this->getInitialParamExample($type)));
			}
			if (isset($val['options'])) {
				if ($val['private'] !== true) {
					new Error($this->errors['controllerMustBePrivate'], array($type, $this->currentClassName, $type, $value, $this->getInitialParamExample($type)));
				}
				if (!is_array($val['options'])) {
					new Error($this->errors['paramNotArray'], array($type, $this->currentClassName, $i, 'options', $type, $value, $this->getInitialParamExample($type)));
				}
				$this->validateObjectFields($val['options'], $value, $type);
				foreach ($val['on'] as $action => $callback) {
					$this->validateCallback($callback, $value, $type, $i, 'callback');
					$this->currentClass['onActions'][] = array('controller' => $val['controller'], 'action' => $action);
				}
			}
			$this->addControllerToClass($val['controller']);
		}
	}

	private function addActionsToClass($actions) {
		$this->currentClass['actions'] = $actions;
	}

	private function addControllerToClass($controller) {
		if (!is_array($this->currentClass['controllers'])) {
			$this->currentClass['controllers'] = array();
		}
		$this->currentClass['controllers'][] = $controller;
	}
}