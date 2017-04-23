<?php

	$data = array(
		'name' => CONST_USER,
		'mode' => 3,
		'before' => "
			var userOptions = ".CONST_USEROPTIONS.";
			var app, status = {},
			attributes = {}, settings = {}, loaded = false, 
			loadRequest, saveRequest;
		",
		'privateMethods' => array(
			'initOptions' => array(
				'body' => "
					if (isObject(userOptions)) {
						if (userOptions['login'] && isString(userOptions['login'])) {
							var ajr = ".CONST_GLOBAL.".get('AjaxRequest');
							loadRequest = new ajr(userOptions['login'], this.setData.bind(this));
						}
					}
				"
			),
			'getDefaultAttributes' => array(
				'body' => "
					return {
						'type': 'guest',
						'accessLevel': 0
					};
				"
			)
		),
		'thisMethods' => array(
			'load' => array(
				'args' => array('application'),
				'body' => "
					if (!loaded) {
						initOptions.call(this);
						app = application;
						if (loadRequest) {
							loadRequest.execute();
							return;
						}
					}
					onLoad(getDefaultAttributes());
				"
			),
			'setData' => array(
				'args' => array('data'),
				'body' => "
					status = data['status'];
					attributes = data['attributes'];
					settings = data['settings'];
					loaded = true;
					if (isComponentLike(app)) {
						app.run();
					}
				"
			),
			'hasFullAccess' => array(
				'body' => "
					var fullAccess = Objects.get(userOptions, 'fullAccess', null);
					var accessLevel = ~~status['accessLevel'];
					return !isNumber(fullAccess) || accessLevel >= fullAccess;
				"
			),
			'isAdmin' => array(
				'body' => "
					var adminAccess = Objects.get(userOptions, 'adminAccess', null);
					var accessLevel = ~~status['accessLevel'];
					return !isNumber(adminAccess) || accessLevel >= adminAccess;
				"
			),
			'isBlocked' => array(
				'body' => "
					return !!status['isBlocked'];
				"
			),
			'getBlockedReason' => array(
				'body' => "
					return status['blockReason'];
				"
			),
			'hasAccessLevel' => array(
				'args' => array('accessLevel', 'isEqual'),
				'body' => "
					if (!isEqual) {
						return status['accessLevel'] >= accessLevel;
					}
					return status['accessLevel'] == accessLevel;	
				"
			),
			'hasType' => array(
				'args' => array('userType'),
				'body' => "
					return status['type'] == userType;
				"
			),
			'isAuthorized' => array(
				'body' => "
					return status['accessLevel'] > 0;
				"
			),
			'getAttributes' => array(
				'body' => "
					return attributes;
				"
			),
			'getAttribute' => array(
				'args' => array('attributeName'),
				'body' => "
					return attributes[attributeName];
				"
			),
			'setAttribute' => array(
				'args' => array('attributeName', 'attributeValue', 'isToSave'),
				'body' => "
					var attrs = {};
					attrs[attributeName] = attributeValue;
					this.setAttributes(attrs, isToSave);
				"
			),
			'setAttributes' => array(
				'args' => array('attrs, isToSave'),
				'body' => "
					if (isObject(attrs)) {
						for (var k in attrs) {
							attributes[k] = attrs[k];
						}		
						if (isToSave && saveRequest) {
							saveRequest.execute(attributes);
						}
					}
				"
			),
			'getSettings' => array(
				'body' => "
					return settings;
				"
			),
			'getSetting' => array(
				'args' => array('settingName'),
				'body' => "
					return settings[settingName];
				"
			),
			'setSetting' => array(
				'args' => array('settingName', 'settingValue'),
				'body' => "
					settings[settingName] = settingValue;
					if (saveRequest) {
						saveRequest.execute({'isSetting': true, 'name': settingName, 'value': settingValue});
					}
				"
			)
		)
	);
?>