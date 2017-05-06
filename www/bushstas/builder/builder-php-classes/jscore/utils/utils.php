<?php

	$data = array(
		'mode' => 5,
		'functions' => array(
			'generateRandomKey' => array(
				'body' => "
					var x = 2147483648, now = +new Date();
					return Math.floor(Math.random() * x).toString(36) + Math.abs(Math.floor(Math.random() * x) ^ now).toString(36);
				"
			),
			'toCamelCase' => array(
				'args' => array('str'),
				'body' => "
					return String(str).replace(/\-([a-z])/g, function(all, match) {
						return match.toUpperCase();
					});
				"
			),
			'isComponentLike' => array(
				'args' => array('a'),
				'body' => "
					return isObject(a) && isFunction(a.instanceOf);
				"
			),
			'isComponent' => array(
				'args' => array('a'),
				'body' => "
					return isComponentLike(a) && a.instanceOf('Component');
				"
			),
			'isController' => array(
				'args' => array('a'),
				'body' => "
					return isComponentLike(a) && a.instanceOf('Controller');
				"
			),
			'isControl' => array(
				'args' => array('a'),
				'body' => "
					return isComponentLike(a) && a.instanceOf('Control');
				"
			),
			'isObject' => array(
				'args' => array('a'),
				'body' => "
					return !!a && typeof a == 'object' && !isNode(a) && !isArray(a);
				"
			),
			'isArray' => array(
				'args' => array('a'),
				'body' => "
					return a instanceof Array;
				"
			),
			'isArrayLike' => array(
				'args' => array('a'),
				'body' => "
					return isArray(a) || isObject(a);
				"
			),
			'isElement' => array(
				'args' => array('a'),
				'body' => "
					return a instanceof Element;
				"
			),
			'isNode' => array(
				'args' => array('a'),
				'body' => "
					return a instanceof Node;
				"
			),
			'isText' => array(
				'args' => array('a'),
				'body' => "
					return a instanceof Text;
				"
			),
			'isFunction' => array(
				'args' => array('a'),
				'body' => "
					return a instanceof Function;
				"
			),
			'isBool' => array(
				'args' => array('a'),
				'body' => "
					return typeof a == 'boolean';
				"
			),
			'isString' => array(
				'args' => array('a'),
				'body' => "
					return typeof a == 'string';
				"
			),
			'isNumber' => array(
				'args' => array('a'),
				'body' => "
					return typeof a == 'string';
				"
			),
			'isPrimitive' => array(
				'args' => array('a'),
				'body' => "
					return isString(a) || isNumber(a) || isBool(a);
				"
			),
			'isNumeric' => array(
				'args' => array('a'),
				'body' => "
					return isNumber(a) || (isString(a) && (/^\d+$/).test(a));
				"
			),
			'isUndefined' => array(
				'args' => array('a'),
				'body' => "
					return a === undefined;
				"
			),
			'isNull' => array(
				'args' => array('a'),
				'body' => "
					return a === null;
				"
			),
			'isNone' => array(
				'args' => array('a'),
				'body' => "
					return isUndefined(a) || isNull(a) || a === false || a === 0 || a === '0' || a === '';
				"
			),
			'isZero' => array(
				'args' => array('a'),
				'body' => "
					return a === 0 || a === '0';
				"
			),
			'isNotEmptyString' => array(
				'args' => array('a'),
				'body' => "
					return isString(a) && (/[^\s]/).test(a);
				"
			),
			'stringToNumber' => array(
				'args' => array('str'),
				'body' => "
					return Number(str);
				"
			),
			'getCount' => array(
				'args' => array('a'),
				'body' => "
					return isArray(a) ? a.length : 0;
				"
			)
		)
	);

?>