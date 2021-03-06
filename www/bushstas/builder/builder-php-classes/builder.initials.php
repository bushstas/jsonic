<?php

class InitialsParser 
{	
	private $currentClass, $currentClassName, $currentObject;
	private $errors = array(
		'incorrectInitial' => '������ � �������� ���� initial ��������� � ������ {??}<xmp>initial {?}</xmp>�������� ������ ����� ��� <b>initial props = {...}</b> ��� <b>initial controllers = [...]</b>',
		'unknownInitial' => '����������� initial �������� {??} � ������ {??}',
		'parseError' => '������ � �������� ���� initial ��������� {??} � ������ {??}<xmp>initial {?} = {?}</xmp><b>�������� ������ ����� ���:</b> {?}',
		'notAssocArray' => 'Initial �������� {??} � ������ {??} ������ ���� ������������� �������� (��������).<xmp>initial {?} = {?}</xmp><b>�������� ������ ����� ���:</b> {?}',
		'notArray' => 'Initial �������� {??} � ������ {??} ������ ���� ������� ��������.<xmp>initial {?} = {?}</xmp><b>�������� ������ ����� ���:</b> {?}',
		'noPatternKey' => 'Initial �������� {??} � ������ {??} ����� ���� {??} �� ��������������� �������� <b>^[a-z]\w*$</b><xmp>initial {?} = {?}</xmp>',
		'incorrectValue' => 'Initial �������� {??} � ������ {??} ����� ���� {??}, � ������� �������� {??} ����� ������������ �������� {??}<xmp>initial {?} = {?}</xmp><b>�������� ������ ����� ���:</b> {?}',
		'incorrectValue2' => 'Initial �������� {??} � ������ {??} ����� ���� {??} � ������������ ��������� {??}<xmp>initial {?} = {?}</xmp>��������� ������� ���������� ���� <b>this.{?}</b><br><br><b>�������� ������ ����� ���:</b> {?}',
		'incorrectBind' => 'Initial �������� {??} � ������ {??} ����� ���� {??}, � ������� �������� {??} ����� ������������ ������ �������� {??} ������ bind ��� ������ {??}<br><br>��������� <b>this</b> ��� <b>null</b><xmp>initial {?} = {?}</xmp><b>�������� ������ ����� ���:</b> {?}',
		'incorrectBind2' => 'Initial �������� {??} � ������ {??} ����� ���� {??}, � ������� �������� {??} ����� ������������ �������� {??} ������ bind  ��� ������ {??}<br><br>��������� JSON �������� ������ ��� ��������� ���������� ���� <b>@shortcut</b><xmp>initial {?} = {?}</xmp><b>�������� ������ ����� ���:</b> {?}',
		'fieldNotAssocArray' => 'Initial �������� {??} � ������ {??} ����� ���� {??}, ������� �� �������� ������������� ��������<xmp>initial {?} = {?}</xmp><b>�������� ������ ����� ���:</b> {?}',
		'fieldEmpty' => 'Initial �������� {??} � ������ {??} ����� ���� {??}, ������� �������� ������<xmp>initial {?} = {?}</xmp><b>�������� ������ ����� ���:</b> {?}',
		'noUrl' => 'Initial �������� {??} � ������ {??} ����� ���� {??}, � ������� ����������� �������� <b>url</b><xmp>initial {?} = {?}</xmp><b>�������� ������ ����� ���:</b> {?}',
		'noController' => 'Initial �������� {??} � ������ {??} �� ����� ���� <b>controller</b><xmp>initial {?} = {?}</xmp><b>�������� ������ ����� ���:</b> {?}',
		'incorrectAsync' => 'Initial �������� {??} � ������ {??} ����� ���� <b>async</b>, �� ��� ��� �� <b>boolean</b><xmp>initial {?} = {?}</xmp><b>�������� ������ ����� ���:</b> {?}',
		'incorrectOptions' => 'Initial �������� {??} � ������ {??} ����� ���� <b>options</b>, �� �� �� �������� ������������� ��������<xmp>initial {?} = {?}</xmp><b>�������� ������ ����� ���:</b> {?}',
		'itemNotAssocArray' => 'Initial �������� {??} � ������ {??} ����� ������� � �������� {??}, ������� �� �������� ������������� ��������<xmp>initial {?} = {?}</xmp><b>�������� ������ ����� ���:</b> {?}',
		'itemNoParam' => 'Initial �������� {??} � ������ {??} ����� ������� � �������� {??}, � �������� ����������� �������� {??}><xmp>initial {?} = {?}</xmp><b>�������� ������ ����� ���:</b> {?}',
		'paramNotArray' => 'Initial �������� {??} � ������ {??} ����� ������� � �������� {??}, � �������� �������� {??} �� �������� ��������<xmp>initial {?} = {?}</xmp><b>�������� ������ ����� ���:</b> {?}',
		'typeError' => '����� {??} � ����� {??} �� ����� ��������� initial �������� {??}',
		'noActions' => '� ����������� {??} ����������� initial �������� <b>actions</b>.<br><br><b>�������� ������ ����� ���:</b> {?}',
		'privateParamNotBool' => 'Initial �������� {??} � ������ {??} ����� ���� <b>private</b>, ������� �� �������� true ��� false<xmp>initial {?} = {?}</xmp><b>�������� ������ ����� ���:</b> {?}',
		'controllerMustBePrivate' => 'Initial �������� {??} � ������ {??} ����� ���� <b>options</b>, �� ��� ���� <b>private</b> ����������� ��� �� �������� true<br>������ ������� � ����������� ��������, ����������� ����� ������������ �������� <b>options</b><xmp>initial {?} = {?}</xmp><b>�������� ������ ����� ���:</b> {?}',
		'unknownMouseEvent' => 'Initial �������� <b>events</b> � ������ {??} �������� ���������������� ������� {??}<xmp>initial {?} = {?}</xmp>������ �������������� �������:<xmp>{?}</xmp>',
		'emptyMouseEvents' => 'Initial �������� <b>events</b> � ������ {??} �������� ���� {??}, ������� �� �������� ���������<xmp>initial {?} = {?}</xmp><b>�������� ������ ����� ���:</b> {?}'
	); 

	private $componentLikeClassTypes = array('component', 'dialog', 'form', 'control', 'menu', 'view', 'application');
	private $availableInitials = array('loader', 'controllers', 'props', 'globals', 'actions', 'options', 'helpers', 'followers', 'correctors', 'listeners', 'events');
	private $initials = array();
	private $classNames = array();
	private $originalInitials = array();
	private $regexp = '/\binitial\s+([\s\S]+?)(?=(initial|function|@EOF))/';
	private $regexp2 = '/^([a-zA-Z]\w*)\s*=\s*([\s\S]+?)[;\s]*$/';
	private $actionsCache = array();
	private $mouseEvents = array(
		'click', 'dblclick', 'mousedown', 'mouseup', 'mouseover', 'mouseout', 'mouseleave', 'mouseenter', 'contextmenu'
	);

	public function fetch(&$content, &$class) {
		$this->defineForClass($class);
		$content .= '@EOF';
		preg_match_all($this->regexp, $content, $matches);
		$initials = $matches[1];
		$this->originalInitials[$this->currentClassName] = $initials;
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
		InitialsSyntaxParser::initClassNames($classes);
		foreach ($classes as $className => $data) {
			if ($data['type'] != 'controller') {
				$this->classNames[] = $className;
			}
		}
		foreach ($this->initials as $className => &$initials) {
			InitialsSyntaxParser::initClassName($className);
			if (!isset($classes[$className])) continue;
			$this->defineForClass($classes[$className]);
			foreach ($initials as $type => &$value) {
				$this->validateInitialValue($type, $value, $classNames);
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

	private function validateInitialValue($type, &$value) {
		$data = InitialsSyntaxParser::parse($value, $type);
		//Printer::log($data);
		$this->currentObject = $data['data'];
		
		switch ($type) {
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

			case 'listeners':
				$this->validateListenersInitials($value, $type);
			break;

			case 'events':
				$this->validateEventsInitials($value, $type);
			break;

			case 'helpers':
				$this->validateHelpersInitials($value, $type);
				$value = preg_replace('/\bhelper([\'"]*)\s*:\s*([A-Z]\w*)/', "helper$1:'$2'", $value);
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
		$value = $data['code'];
	}

	public function get() {
		return $this->initials;
	}

	public function getOriginal() {
		return $this->originalInitials;
	}

	public function getControllerActions($ctr) {
		return $this->actionsCache[$ctr];
	}

	private	function getInitialParamExample($type) {
		switch ($type) {
			case 'actions':
				return "<xmp>initial actions = {\n\t'load': {\n\t\t'url': './path',\n\t\t'method': 'GET',\n\t\t'callback': this.onLoad\n\t}\n}</xmp><b>���</b><xmp>initial actions = {\n\t'load': {\n\t\t'url': './path',\n\t\t'method': 'POST',\n\t\t'callback': this.onLoad.bind(this, ...args)\n\t}\n}</xmp>";
			case 'listeners':
				return "<xmp>initial listeners = {\n\t'globalEventName': this.handleGlobalEvent\n}</xmp>";
			case 'events':
				return "<xmp>initial events = {\n\t'click': {\n\t\t'class-name': this.onClick\n\t},\n\t'contextmenu': {\n\t\t'class-name': this.onContextMenu\n\t}\n}</xmp>";
			case 'loader':
				return "<xmp>initial loader = {\n\t'controller': ControllerClass,\n\t'async': true,\n\t'callback': this.handleLoad,\n\t'options': {\n\t\t'key': 'value'\n\t}\n}</xmp>";
			case 'options':
				return "<xmp>initial options = {\n\t'key': 'id',\n\t'store': true,\n\t'storeAs': 'items',\n\t'storePeriod': '1day',\n\t'clone': true\n}</xmp>";
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

	private function validateObjectFields($obj, $value, $type, $novalidation = false) {
		if (is_array($obj)) {
			foreach ($obj as $key => $val) {
				if (!$novalidation && !preg_match('/^[a-z]\w*$/i', $key)) {
					new Error($this->errors['noPatternKey'], array($type, $this->currentClassName, $key, $type, $value));
				}
				if ($this->isAssocArray($val)) {
					$this->validateObjectFields($val, $value, $type, $novalidation);
				}
			}
		}
	}

	private function validateCallback($callback, $value, $type, $field, $name = '', $val = '') {
		if (empty($callback)) return;

		$callback = str_replace('<nq>', '', $callback);
		if (!preg_match('/^this\.[a-z]\w*$/i', $callback)) {
			if (!empty($name)) {
				new Error($this->errors['incorrectValue'], array($type, $this->currentClassName, $field, $name, $callback, $type, $value, $this->getInitialParamExample($type)));
			} else {
				new Error($this->errors['incorrectValue2'], array($type, $this->currentClassName, $field, $val, $type, $value, $type == 'globals' ? 'onChangeGlobalVar' : 'onChangeSomeProp', $this->getInitialParamExample($type)));
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
		$callback = str_replace('this.', '', $callback);
		if (!is_array($this->currentClass['initialCallbacks'])) {
			$this->currentClass['initialCallbacks'] = array();
		}
		$this->currentClass['initialCallbacks'][] = $callback;
	}

	private function validateDefaultInitials($value, $type) {
		$initials = $this->currentObject;
		if ($type == 'props' && !in_array($this->currentClass['type'], $this->componentLikeClassTypes)) {
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
		$this->validateCallback($initials['callback'], $value, $type, 'callback');
		$controller = str_replace('<nq>', '', $initials['controller']);
		$this->addControllerToClass($controller);
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

	private function validateListenersInitials($value, $type) {
		$initials = $this->currentObject;
		if (!$this->isAssocArray($initials, $value)) {
			$this->initialAssocArrayTypeError($value, $type);
		}
		$this->validateObjectFields($initials, $value, $type);
		foreach ($initials as $key => $val) {
			$this->validateCallback($val, $value, $type, $key, '', $val);
		}
	}

	private function validateEventsInitials($value, $type) {
		$initials = $this->currentObject;
		if (!$this->isAssocArray($initials, $value)) {
			$this->initialAssocArrayTypeError($value, $type);
		}
		foreach ($initials as $key => $val) {
			if (!in_array($key, $this->mouseEvents)) {
				new Error($this->errors['unknownMouseEvent'], array($this->currentClassName, $key, $type, $value, implode("\n", $this->mouseEvents)));
			}
			if (!$this->isAssocArray($initials, $val)) {
				$this->initialAssocArrayTypeError($val, $type);
			}
			if (empty($val)) {
				new Error($this->errors['emptyMouseEvents'], array($this->currentClassName, $key, $type, $value, $this->getInitialParamExample($type)));	
			}
		}
		// $this->validateObjectFields($initials, $value, $type, true);
		// foreach ($initials as $key => $val) {
		// 	$this->validateCallback($val, $value, $type, $key, '', $val);
		// }
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
			$controller = str_replace('<nq>', '', $val['controller']);
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
					$this->currentClass['onActions'][] = array('controller' => $controller, 'action' => $action);
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
					$this->currentClass['onActions'][] = array('controller' => $controller, 'action' => $action);
				}
			}			
			$this->addControllerToClass($controller);
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