<?php

class RoutesCompiler 
{
	private $configProvider, $config, $routerMenu,
			$defaultRoute, $indexRoute, $isHashRouter;

	private $routes = array();
	private $routeControllersToLoad = array();
	private $disabledRoutes = array();
	private $disabledRouteNames = array();
	private $routeControllersByViews = array();
	private $errorRoutes = array();
	private $errorCodes = array('404', '401');

	private $errors = array(
		'incorrectRouter' => 'Параметр конфигурации <b>router</b> отсутствует или не является массивом',
		'incorrectRoutes' => "Параметр конфигурации <b>router['routes']</b> отсутствует или не является массивом",
		'routeIsNotAnArray' => "Один из пунктов параметра конфигурации <b>routes</b> не является массивом",
		'noRouteName' => "Параметр name = '{??}' одного из пунктов <b>routes</b> отсутствует или не является строкой",
		'noRouteView' => "Параметр view = '{??}' у маршрута с именем {??} отсутствует или не является строкой",
		'forbiddenNameSymbols' => "Параметр name = '{??}' одного из пунктов <b>routes</b> содержит запрещенные символы",
		'noPatternName' => "Параметр name = '{??}' одного из пунктов <b>routes</b> не соответствует паттерну <b>[a-z]\w+</b>",
		'forbiddenViewSymbols' => "Параметр view = '{??}' у маршрута с именем {??} содержит запрещенные символы",
		'noPatternView' => "Параметр view = '{??}' у маршрута с именем {??} не соответствует паттерну <b>[A-Z]\w+</b>",
		'titleIsNotString' => "Параметр <b>title</b> у маршрута с именем {??} не является строкой",
		'noPatternTitle' => "Значение title = '{??}' у маршрута с именем {??} содержащее символ $ не соответствует паттерну <b>^\\$[a-z]\w+$</b>",
		'accessLevelNotNumber' => "Параметр <b>accessLevel</b> у маршрута с именем {??} не является целым положительным числом",
		'paramsAreNotAnArray' => "Параметр <b>params</b> у маршрута с именем {??} не является ассоциативным массивом",
		'paramKeyNotString' => "Ключ <b>params['{?}']</b> у маршрута с именем {??} не является строкой",
		'paramKeyHasForbiddenSymbols' => "Ключ <b>params['{?}']</b> у маршрута с именем {??} содержит запрещенные символы",
		'noPatternParamKey' => "Значение <b>params['{?}']</b> у маршрута с именем {??} содержащее символ $ не соответствует паттерну <b>^\\$\d+$</b>",
		'loadIsNotAnArray' => "Параметр <b>load</b> у маршрута с именем {??} не является массивом",
		'loadItemNotString' => "Один из элементов параметра <b>load</b> у маршрута с именем {??} не является строкой",
		'loadItemEmpty' => "Один из элементов параметра <b>load</b> у маршрута с именем {??} пуст",
		'childrenNotAnArray' => "Параметр <b>children</b> одного из пунктов routes не является массивом",
		'menuNotString' => "Параметр конфигурации <b>router['menu']</b> не является строкой",
		'noPatternMenuClass' => "Параметр конфигурации <b>router['menu']</b> содержит название класса {??} не удовлетворяющее паттерну ^[A-Z]\w*$",
		'noDefaultAnd404Routes' => "Параметры конфигурации <b>router['defaultRoute']</b> и <b>router['404']</b> оба отсутствуют. Хотя один из них должен обязательно присутствовать",
		'defaultRouteNotString' => "Параметр конфигурации <b>router['defaultRoute']</b> не является строкой",
		'defaultRouteNotFound' => "Параметр конфигурации <b>router['defaultRoute']</b> = '{??}' не найден среди указанных в <b>router['routes']</b>",
		'indexRoutNotString' => "Параметр конфигурации <b>router['indexRoute']</b> отсутствует или не является строкой",
		'indexRoutNotFound' => "Параметр конфигурации <b>router['indexRoute']<b/> = '{??}' не найден среди указанных в <b>router['routes']</b>",
		'incorrectHash' => "Параметр конфигурации <b>router['hash']</b> должен быть равен null, true или false",
		'errorRouteNotString' => "Параметр <b>router['{?}']</b> не является строкой",
		'errorRouteHasForbiddenSymbols' => "Параметр <b>router['{?}']</b> = '{??}' содержит запрещенные символы",
		'noPatternErrorRoute' => "Параметр <b>router['{?}']</b> = '{??}' не соответствует паттерну [A-Z]\w+",
		'incorrectDisabled' => "Параметр <b>disabled</b> у маршрута с именем {??} должен иметь значение с типом <b>boolean</b>",
		'defaultRouteDisabled' => "Параметр конфигурации <b>router['defaultRoute']</b> ссылается на маршрут {??}, который отключен",
		'indexRouteDisabled' => "Параметр конфигурации <b>router['indexRoute']</b> ссылается на маршрут {??}, который отключен",
		'errorRouteDisabled' => "Параметр конфигурации <b>router['{?}']</b> ссылается на маршрут {??}, который отключен",
	);

	public function __construct($configProvider) {
		$this->configProvider = $configProvider;
	}

	public function init() {
		$this->config = $this->configProvider->getRoutesConfig();
		if (empty($this->config) || !is_array($this->config)) {
			new Error($this->errors['incorrectRouter']);
		}
		$this->routes = $this->config['routes'];
		if (empty($this->routes) || !is_array($this->routes)) {
			new Error($this->errors['incorrectRoutes']);
		}
		$this->validateRoutes($this->routes);
		$this->validateRouteMenu();
		$this->validateDefaultRoute();
		$this->validateIndexRoute();
		$this->validateErrorRoutes();
		
		$this->isHashRouter = $this->config['hash'];
		if ($this->isHashRouter !== null && !is_bool($this->isHashRouter)) {
			new Error($this->errors['incorrectHash']);
		}
	}

	public function getDisabledRoutes() {
		return $this->disabledRoutes;
	}

	public function getRoutes() {
		return $this->routes;
	}

	public function getRouteViews() {
		$views = array();
		foreach ($this->config['routes'] as $route) {
			$views[] = $route['view'];
		}
		foreach ($this->errorCodes as $errorCode) {
			if (!empty($this->config[$errorCode])) {
				$views[] = $this->config[$errorCode];
			}
		}
		return $views;
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

	private	function validateRoutes(&$routes) {
		$enabledRoutes = array();
		foreach ($this->routes as &$route) {
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
				if (!is_numeric($route['accessLevel'])) {
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
			if (isset($route['disabled'])) {
				if (!is_bool($route['disabled'])) {
					new Error($this->errors['incorrectDisabled'], array($route['name']));
				}
				if ($route['disabled']) {
					$this->disabledRoutes[] = $route['view'];
					$this->disabledRouteNames[] = $route['name'];
				}
			}
			if (empty($route['disabled'])) {
				$enabledRoutes[] = $route;
			}
		}
		$routes = $enabledRoutes;
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
		if (in_array($this->defaultRoute, $this->disabledRouteNames)) {
			new Error($this->errors['defaultRouteDisabled'], array($this->defaultRoute));
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
		if (in_array($this->indexRoute, $this->disabledRouteNames)) {
			new Error($this->errors['indexRouteDisabled'], array($this->indexRoute));
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
		if (in_array($route, $this->disabledRoutes)) {
			new Error($this->errors['errorRouteDisabled'], array($name, $route));
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