function User() {
	var app;
	var loadedItems = 0;
	var attributes = {};
	var loaded = false;
	var loadRequest;
	var saveRequest;

	var initOptions = function() {
		var userOptions = __USEROPTIONS;
		if (isObject(userOptions)) {
			if (userOptions['login'] && isString(userOptions['login'])) {
				loadRequest = new AjaxRequest(userOptions['login'], onLoad.bind(this));
			}
		}
	};
	var loadDictionary = function() {
		Dictionary.load(onLoadDictionary.bind(this));
	};
	this.load = function(application) {
		if (!loaded) {
			initOptions();
			loadDictionary();
			app = application;
			if (loadRequest) {
				loadRequest.execute();
				return;
			}
		}
		onLoad(getDefaultAttributes());
	};
	var onLoad = function(attrs) {
		attributes = attrs;
		loadedItems++;
		onLoadItem();
	};
	var onLoadDictionary = function() {
		loadedItems++;
		onLoadItem();
	};
	var onLoadItem = function() {
		if (loadedItems == 2) {
			loaded = true;
			if (app instanceof Function) {
				app = new app();
				Initialization.initiate.call(app);
				app.run();
			}
		}
	};
	this.hasFullAccess = function() {
		var fullAccess = Objects.get(__USEROPTIONS, 'fullAccess', null);
		var accessLevel = ~~attributes['accessLevel'];
		return !isNumber(fullAccess) || accessLevel >= fullAccess;
	};
	this.hasAccessLevel = function(accessLevel, isEqual) {
		if (!isEqual) {
			return attributes['accessLevel'] >= accessLevel;
		}
		return attributes['accessLevel'] == accessLevel;
	};
	this.hasType = function(userType) {
		return attributes['type'] == userType;
	};
	this.isAuthorized = function() {
		return attributes['accessLevel'] > 0;
	};
	this.getAttribute = function(attributeName) {
		return attributes[attributeName];
	};
	this.setAttribute = function(attributeName, attributeValue, isToSave) {
		var attrs = {};
		attrs[attributeName] = attributeValue;
		this.setAttributes(attrs, isToSave);
	};
	this.setAttributes = function(attrs, isToSave) {
		if (isObject(attrs)) {
			for (var k in attrs) {
				attributes[k] = attrs[k];
			}		
			if (isToSave && saveRequest) {
				saveRequest.execute(attributes);
			}
		}
	};
	var getDefaultAttributes = function() {
		return {
			'type': 'guest',
			'accessLevel': 0
		};
	};
}