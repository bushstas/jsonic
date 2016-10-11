function User() {
	var userOptions = {{USEROPTIONS}};
	var app, loadedItems = 0, status = {},
	attributes = {}, settings = {}, loaded = false, 
	loadRequest, saveRequest;

	var initOptions = function() {
		if (isObject(userOptions)) {
			if (userOptions['login'] && isString(userOptions['login'])) {
				loadRequest = new AjaxRequest(userOptions['login'], onLoad.bind(this));
			}
		}
	};
	var loadDictionary = function() {
		if (typeof Dictionary != 'undefined') {
			Dictionary.load(onLoadDictionary.bind(this));
		}
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
	var onLoad = function(data) {
		status = data['status'];
		attributes = data['attributes'];
		settings = data['settings'];
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
			if (isComponentLike(app)) {
				app.run();
			}
		}
	};
	this.hasFullAccess = function() {
		var fullAccess = Objects.get(userOptions, 'fullAccess', null);
		var accessLevel = ~~status['accessLevel'];
		return !isNumber(fullAccess) || accessLevel >= fullAccess;
	};
	this.isAdmin = function() {
		var adminAccess = Objects.get(userOptions, 'adminAccess', null);
		var accessLevel = ~~status['accessLevel'];
		return !isNumber(adminAccess) || accessLevel >= adminAccess;
	};
	this.isBlocked = function() {
		return !!status['isBlocked'];
	};
	this.getBlockedReason = function() {
		return status['blockReason'];
	};
	this.hasAccessLevel = function(accessLevel, isEqual) {
		if (!isEqual) {
			return status['accessLevel'] >= accessLevel;
		}
		return status['accessLevel'] == accessLevel;
	};
	this.hasType = function(userType) {
		return status['type'] == userType;
	};
	this.isAuthorized = function() {
		return status['accessLevel'] > 0;
	};
	this.getAttributes = function() {
		return attributes;
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
	this.getSettings = function() {
		return settings;
	};
	this.getSetting = function(settingName) {
		return settings[settingName];
	};
	this.setSetting = function(settingName, settingValue) {
		settings[settingName] = settingValue;
		if (saveRequest) {
			saveRequest.execute({'isSetting': true, 'name': settingName, 'value': settingValue});
		}
	};
	var getDefaultAttributes = function() {
		return {
			'type': 'guest',
			'accessLevel': 0
		};
	};
}