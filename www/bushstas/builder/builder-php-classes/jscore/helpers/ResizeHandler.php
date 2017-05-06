<?php

	$data = array(
		'name' => 'ResizeHandler',
		'mode' => 2,
		'before' => "
			var subscribers = [], timer;
		",
		'after' => "
			window.addEventListener('resize', onResize, false);
		",
		'privateMethods' => array(
			'onResize' => array(
				'body' => "
					window.clearTimeout(timer);
					timer = window.setTimeout(function() {
						for (var i = 0; i < subscribers.length; i++) {
							var callback = ".CONST_OBJECTS.".get(subscribers[i][1], 'callback');
							if (isFunction(callback)) callback.call(subscribers[i][0]);
						}
					}, 200);
				"
			)
		),
		'thisMethods' => array(
			'subscribe' => array(
				'args' => array('subscriber', 'options'),
				'body' => "
					subscribers.push([subscriber, options]);
				"
			)
		)
	)
?>