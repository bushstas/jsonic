<?php

	$data = array(
		'name' => 'InputHandler',
		'mode' => 2,
		'before' => "
			var subscribers = [];
			var options = [];
		",
		'privateMethods' => array(
			'onKeyup' => array(
				'args' => array('index', 'e'),
				'body' => "
					var charCode = e.charCode;
					var keyName = getKeyName(charCode);
					var opts = options[index];
					var subscriber = subscribers[index];
					var cb = opts['callbacks'];
					var value = e.target.value;
					if (keyName && isFunction(cb[keyName])) callSubscriber(index, keyName, value);
					else if (isFunction(cb[charCode])) callSubscriber(index, charCode, value);
				"
			),
			'getKeyName' => array(
				'args' => array('charCode'),
				'body' => "
					return ({'13': 'enter', '27': 'esc', '38': 'up', '40': 'down', '37': 'left', '39': 'right'})[charCode];
				"
			),
			'onEvent' => array(
				'args' => array('index', 'eventName', 'e'),
				'body' => "
					callSubscriber(index, eventName, e.target.value);
				"
			),
			'callSubscriber' => array(
				'args' => array('index', 'eventName', 'value'),
				'body' => "
					var s = subscribers[index], r;
					var cb = ".CONST_OBJECTS.".get(options[index]['callbacks'], eventName);
					if (isFunction(cb)) r = cb.call(s, value);
					if (r !== false && isString(eventName)) s.dispatchEvent(eventName, value);
				"
			)
		),
		'thisMethods' => array(
			'subscribe' => array(
				'args' => array('subscriber', 'opts'),
				'body' => "
					if (isObject(opts['callbacks']) && isString(opts['inputSelector']) && subscribers.indexOf(subscriber) == -1) {			
						var input = subscriber.findElement(opts['inputSelector']);
						var actions = ".CONST_OBJECTS.".getKeys(opts['callbacks']);
						if (input) {
							opts['input'] = input;
							subscribers.push(subscriber);
							options.push(opts);
							var index = subscribers.length - 1;
							if (actions.hasExcept('focus', 'blur', 'input')) input.addEventListener('keyup', onKeyup.bind(null, index), false);
							if (actions.has('input')) input.addEventListener('input', onEvent.bind(null, index, 'input'), false);
							if (actions.has('focus')) input.addEventListener('focus', onEvent.bind(null, index, 'focus'), false);
							if (actions.has('blur')) input.addEventListener('blur', onEvent.bind(null, index, 'blur'), false);
						}
					}
				"
			)
		)
	)
?>