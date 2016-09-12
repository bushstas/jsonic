<?php

class RoutesCompiler 
{
	private $configProvider, $config;
	private $routeControllersToLoad = array();
	private $routeControllersByViews = array();

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
		'accessLevelNotNumber' => "Параметр <b>accessLevel</b> у маршрута с именем {??} не является числом",
		'paramsAreNotAnArray' => "Параметр <b>params</b> у маршрута с именем {??} не является ассоциативным массивом",
		'paramKeyNotString' => "Ключ <b>params['{?}']</b> у маршрута с именем {??} не является строкой",
		'paramKeyHasForbiddenSymbols' => "Ключ <b>params['{?}']</b> у маршрута с именем {??} содержит запрещенные символы",
		'noPatternParamKey' => "Значение <b>params['{?}']</b> у маршрута с именем {??} содержащее символ $ не соответствует паттерну <b>^\\$\d+$</b>",
		'loadIsNotAnArray' => "Параметр <b>load</b> у маршрута с именем {??} не является массивом",
		'loadItemNotString' => "Один из элементов параметра <b>load</b> у маршрута с именем {??} не является строкой",
		'loadItemEmpty' => "Один из элементов параметра <b>load</b> у маршрута с именем {??} пуст",
		'childrenNotAnArray' => "Параметр <b>children</b> одного из пунктов routes не является массивом",
		'menuNotString' => "Параметр конфигурации <b>router['menu']</b> не является строкой",
		'noPatternMenuClass' => "Параметр конфигурации <b>router['menu']</b> содержит название класса {??} не удовлетворяющее паттерну ^[A-Z]\w*$"
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


		if (empty($router['defaultRoute']) && empty($router['404'])) {
			error("Параметры конфигурации <b>router['defaultRoute']</b> и <b>router['404']</b> оба отсутствуют. Хотя один из них должен обязательно присутствовать");
		}
		$defaultRoute = null;
		if (!empty($router['defaultRoute'])) {
			if (!is_string($router['defaultRoute'])) {
				error("Параметр конфигурации <b>router['defaultRoute']</b> не является строкой");
			}
			if (!isRoute($router['defaultRoute'], $routes)) {
				error("Параметр конфигурации <b>router['defaultRoute']</b> = '<b>".$router['defaultRoute']."</b>' не найден среди указанных в <b>router['routes']</b>");
			}
			$defaultRoute = $router['defaultRoute'];
		}
		$indexRoute = $router['indexRoute'];
		if (empty($indexRoute) || !is_string($indexRoute)) {
			error("Параметр конфигурации <b>router['indexRoute']</b> отсутствует или не является строкой");
		}
		if (!isRoute($indexRoute, $routes)) {
			error("Параметр конфигурации <b>router['indexRoute']<b/> = '<b>".$indexRoute."</b>' не найден среди указанных в <b>router['routes']</b>");
		}
		$isHashRouter = $router['hash'];
		if ($isHashRouter !== null && !is_bool($isHashRouter)) {
			error("Параметр конфигурации <b>router['hash']</b> должен быть равен null, true или false");
		}
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
			$routerMenu = $properRouterMenu;
		}
	}
}