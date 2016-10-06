<?php

class RoutesCompiler 
{
	private $configProvider, $config, $routerMenu,
			$defaultRoute, $indexRoute, $isHashRouter;

	private $routeControllersToLoad = array();
	private $routeControllersByViews = array();
	private $errorRoutes = array();
	private $errorCodes = array('404', '401');

	private $errors = array(
		'incorrectRouter' => '�������� ������������ <b>router</b> ����������� ��� �� �������� ��������',
		'incorrectRoutes' => "�������� ������������ <b>router['routes']</b> ����������� ��� �� �������� ��������",
		'routeIsNotAnArray' => "���� �� ������� ��������� ������������ <b>routes</b> �� �������� ��������",
		'noRouteName' => "�������� name = '{??}' ������ �� ������� <b>routes</b> ����������� ��� �� �������� �������",
		'noRouteView' => "�������� view = '{??}' � �������� � ������ {??} ����������� ��� �� �������� �������",
		'forbiddenNameSymbols' => "�������� name = '{??}' ������ �� ������� <b>routes</b> �������� ����������� �������",
		'noPatternName' => "�������� name = '{??}' ������ �� ������� <b>routes</b> �� ������������� �������� <b>[a-z]\w+</b>",
		'forbiddenViewSymbols' => "�������� view = '{??}' � �������� � ������ {??} �������� ����������� �������",
		'noPatternView' => "�������� view = '{??}' � �������� � ������ {??} �� ������������� �������� <b>[A-Z]\w+</b>",
		'titleIsNotString' => "�������� <b>title</b> � �������� � ������ {??} �� �������� �������",
		'noPatternTitle' => "�������� title = '{??}' � �������� � ������ {??} ���������� ������ $ �� ������������� �������� <b>^\\$[a-z]\w+$</b>",
		'accessLevelNotNumber' => "�������� <b>accessLevel</b> � �������� � ������ {??} �� �������� ������",
		'paramsAreNotAnArray' => "�������� <b>params</b> � �������� � ������ {??} �� �������� ������������� ��������",
		'paramKeyNotString' => "���� <b>params['{?}']</b> � �������� � ������ {??} �� �������� �������",
		'paramKeyHasForbiddenSymbols' => "���� <b>params['{?}']</b> � �������� � ������ {??} �������� ����������� �������",
		'noPatternParamKey' => "�������� <b>params['{?}']</b> � �������� � ������ {??} ���������� ������ $ �� ������������� �������� <b>^\\$\d+$</b>",
		'loadIsNotAnArray' => "�������� <b>load</b> � �������� � ������ {??} �� �������� ��������",
		'loadItemNotString' => "���� �� ��������� ��������� <b>load</b> � �������� � ������ {??} �� �������� �������",
		'loadItemEmpty' => "���� �� ��������� ��������� <b>load</b> � �������� � ������ {??} ����",
		'childrenNotAnArray' => "�������� <b>children</b> ������ �� ������� routes �� �������� ��������",
		'menuNotString' => "�������� ������������ <b>router['menu']</b> �� �������� �������",
		'noPatternMenuClass' => "�������� ������������ <b>router['menu']</b> �������� �������� ������ {??} �� ��������������� �������� ^[A-Z]\w*$",
		'noDefaultAnd404Routes' => "��������� ������������ <b>router['defaultRoute']</b> � <b>router['404']</b> ��� �����������. ���� ���� �� ��� ������ ����������� ��������������",
		'defaultRouteNotString' => "�������� ������������ <b>router['defaultRoute']</b> �� �������� �������",
		'defaultRouteNotFound' => "�������� ������������ <b>router['defaultRoute']</b> = '{??}' �� ������ ����� ��������� � <b>router['routes']</b>",
		'indexRoutNotString' => "�������� ������������ <b>router['indexRoute']</b> ����������� ��� �� �������� �������",
		'indexRoutNotFound' => "�������� ������������ <b>router['indexRoute']<b/> = '{??}' �� ������ ����� ��������� � <b>router['routes']</b>",
		'incorrectHash' => "�������� ������������ <b>router['hash']</b> ������ ���� ����� null, true ��� false",
		'errorRouteNotString' => "�������� <b>router['{?}']</b> �� �������� �������",
		'errorRouteHasForbiddenSymbols' => "�������� <b>router['{?}']</b> = '{??}' �������� ����������� �������",
		'noPatternErrorRoute' => "�������� <b>router['{?}']</b> = '{??}' �� ������������� �������� [A-Z]\w+"
	);

	public function __construct($configProvider) {
		$this->configProvider = $configProvider;
	}

	public function init() {
		$this->config = $this->configProvider->getRoutesConfig();
		if (empty($this->config) || !is_array($this->config)) {
			new Error($this->errors['incorrectRouter']);
		}


		$routes = $this->config['routes'];
		if (empty($routes) || !is_array($routes)) {
			new Error($this->errors['incorrectRoutes']);
		}
		$this->validateRoutes($routes);
		$this->validateRouteMenu();
		$this->validateDefaultRoute();
		$this->validateIndexRoute();
		$this->validateErrorRoutes();
		
		$this->isHashRouter = $this->config['hash'];
		if ($this->isHashRouter !== null && !is_bool($this->isHashRouter)) {
			new Error($this->errors['incorrectHash']);
		}
	}

	public function getErrorRoutes() {
		return $this->errorRoutes;
	}

	public function getMenu() {
		return $this->routerMenu;
	}

	public function getControllers() {
		return $this->routeControllersToLoad;
	}

	public function getControllersByView() {
		return $this->routeControllersByViews;
	}

	public function getIndexRoute() {
		return $this->indexRoute;
	}

	public function getDefaultRoute() {
		return $this->defaultRoute;
	}

	public function getHashRouter() {
		return $this->isHashRouter;
	}

	private	function validateRoutes($routes) {
		foreach ($routes as $route) {
			$this->routeControllersByViews[$route['view']] = array();
			if (!is_array($route)) {
				new Error($this->errors['routeIsNotAnArray']);
			}
			if (empty($route['name']) || !is_string($route['name'])) {
				new Error($this->errors['noRouteName'], array($route['name']));
			}
			if (empty($route['view']) || !is_string($route['view'])) {
				new Error($this->errors['noRouteView'], array($route['view'], $route['name']));;	
			}
			if (preg_match('/[^\w]/', $route['name'])) {
				new Error($this->errors['forbiddenNameSymbols'], array($route['name']));
			}
			if (!preg_match('/^[a-z]\w*/', $route['name'])) {
				new Error($this->errors['noPatternName'], array($route['name']));
			}			
			if (preg_match('/[^\w]/', $route['view'])) {
				new Error($this->errors['forbiddenViewSymbols'], array($route['view'], $route['name']));
			}
			if (!preg_match('/^[A-Z]\w*/', $route['view'])) {
				new Error($this->errors['noPatternView'], array($route['view'], $route['name']));
			}
			if (isset($route['title'])) {
				if (!is_string($route['title'])) {
					new Error($this->errors['titleIsNotString'], array($route['name']));
				}
				if (preg_match('/\$/', $route['title']) && !preg_match('/^\$[a-z]\w+$/', $route['title'])) {
					new Error($this->errors['noPatternTitle'], array($route['title'], $route['name']));
				}				
			}
			if (isset($route['accessLevel'])) {
				if (!is_int($route['accessLevel'])) {
					new Error($this->errors['accessLevelNotNumber'], array($route['name']));
				}
			}
			if (isset($route['params'])) {
				if (!is_array($route['params'])) {
					new Error($this->errors['paramsAreNotAnArray'], array($route['name']));
				}
				foreach ($route['params'] as $key => $value) {
					if (!is_string($key)) {
						new Error($this->errors['paramKeyNotString'], array($key, $route['name']));
					}
					if (preg_match('/[^\w]/', $key)) {
						new Error($this->errors['paramKeyHasForbiddenSymbols'], array($key, $route['name']));
					}
					if (preg_match('/\$/', $value) && !preg_match('/^\$\d+$/', $value)) {
						new Error($this->errors['noPatternParamKey'], array($key, $route['name']));
					}
				}
			}
			if (isset($route['load'])) {
				if (!is_array($route['load'])) {
					new Error($this->errors['loadIsNotAnArray'], array($route['name']));
				}
				foreach ($route['load'] as $controllerToLoad) {
					if (!is_string($controllerToLoad)) {
						new Error($this->errors['loadItemNotString'], array($route['name']));
					}
					if (empty($controllerToLoad)) {
						new Error($this->errors['loadItemEmpty'], array($route['name']));
					}
					$this->routeControllersByViews[$route['view']][] = $controllerToLoad;
					$this->routeControllersToLoad[] = $controllerToLoad;
				}
			}
			if (isset($route['children'])) {
				if (!is_array($route['children'])) {
					new Error($this->errors['childrenNotAnArray']);
				}
				$this->validateRoutes($route['children']);
			}
		}
		$this->routeControllersToLoad = array_unique($this->routeControllersToLoad);
	}

	private	function validateRouteMenu() {
		if (isset($this->config['menu'])) {
			if (!is_string($this->config['menu'])) {
				new Error($this->errors['menuNotString']);
			}
			$routerMenu = explode(',', preg_replace('/\s/', '', $this->config['menu']));
			$properRouterMenu = array();
			foreach ($routerMenu as $menuClass) {
				if (!empty($menuClass)) {					
					if (!preg_match('/^[A-Z]\w*$/', $menuClass)) {
						new Error($this->errors['noPatternMenuClass'], array($menuClass));
					}
					$properRouterMenu[] = $menuClass;
				}
			}
			$this->routerMenu = $properRouterMenu;
		}
	}

	private function validateDefaultRoute() {
		$this->defaultRoute = $this->config['defaultRoute'];
		if (empty($this->defaultRoute) && empty($this->config['404'])) {
			new Error($this->errors['noDefaultAnd404Routes']);
		}
		if (!empty($this->defaultRoute)) {
			if (!is_string($this->defaultRoute)) {
				new Error($this->errors['defaultRouteNotString']);
			}
			if (!$this->isRoute($this->defaultRoute, $this->config['routes'])) {
				new Error($this->errors['defaultRouteNotFound'], array($this->defaultRoute));
			}
		}
	}

	private function validateIndexRoute() {
		$this->indexRoute = $this->config['indexRoute'];
		if (empty($this->indexRoute) || !is_string($this->indexRoute)) {
			new Error($this->errors['indexRoutNotString']);
		}
		if (!$this->isRoute($this->indexRoute, $this->config['routes'])) {
			new Error($this->errors['indexRoutNotFound'], array($this->indexRoute));
		}
	}

	private function validateErrorRoutes() {
		foreach ($this->errorCodes as $errorCode) {
			if (!empty($this->config[$errorCode])) {
				$this->checkErrorRoute($this->config[$errorCode], $errorCode);
				$this->errorRoutes[$errorCode] = $this->config[$errorCode];
			}
		}
	}

	private	function checkErrorRoute($route, $name) {
		if (!is_string($route)) {
			new Error($this->errors['errorRouteNotString'], array($name));
		}
		if (preg_match('/[^\w]/', $route)) {
			new Error($this->errors['errorRouteHasForbiddenSymbols'], array($name, $route));
		}
		if (!preg_match('/^[A-Z]\w*/', $route)) {
			new Error($this->errors['noPatternErrorRoute'], array($name, $route));
		}		
	}

	private	function isRoute($routeName, $routes) {
		foreach ($routes as $route) {
			if ($route['name'] == $routeName) {
				return true;
			}
			if (is_array($route['children']) && $this->isRoute($routeName, $route['children'])) {
				return true;
			}
		}
		return false;
	}
}