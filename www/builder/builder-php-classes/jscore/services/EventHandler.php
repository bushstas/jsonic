<?php

	$data = array(
		'name' => 'EventHandler',
		'condition' => '!this||this==window',
		'afterCondition' => "
			this.listeners = [];
		",
		'methods' => array(
			'listen' => array(
				'args' => array('element', 'type', 'handler'),
				'body' => "
					this.listeners.push([element, type, handler]);
					element.addEventListener(type, handler, false);
				"
			),
			'listenOnce' => array(
				'args' => array('element', 'type', 'handler'),
				'body' => "
					var cb = function() {
						handler();
						element.removeEventListener(type, cb, false);
					};
					element.addEventListener(type, cb, false);
				"
			),
			'unlisten' => array(
				'args' => array('element', 'type'),
				'body' => "
					var l, i;
					for (i = 0; i < this.listeners.length; i++) {
						l = this.listeners[i];
						if (l && l[0] == element && l[1] == type) {
							l[0].removeEventListener(l[1], l[2], false);
							this.listeners[i] = null;
						}
					}
				"
			),
			'dispose' => array(
				'body' => "
					var l, i;
					for (i = 0; i < this.listeners.length; i++) {
						l = this.listeners[i];
						if (l) {
							l[0].removeEventListener(l[1], l[2], false);
						}
					}
					this.listeners = null;
				"
			)
		)
	);
?>