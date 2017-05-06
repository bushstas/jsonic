<?php

	$data = array(
		'name' => 'MouseHandler',
		'condition' => CONST_ENTERCOND,
		'afterCondition' => "
			this.subscribers = [];
			this.options = [];
			var eh = ".CONST_GLOBAL.".get('EventHandler');
			this.eventHandler = new eh();
		",
		'privateMethods' => array(
			'extendOptions' => array(
				'args' => array('index', 'opts'),
				'body' => "
					".CONST_OBJECTS.".merge(this.options[index], opts);
				"
			),
			'onClick' => array(
				'args' => array('subscriber', 'e'),
				'body' => "
					var index = this.subscribers.indexOf(subscriber);
					var opts = this.options[index];
					var target;
					for (var k in opts) {
						target = e.getTargetWithClass(k, true);
						if (target) {
							if (isFunction(opts[k])) {
								opts[k].call(subscriber, target, e);
								e.stopPropagation();
								break;
							}
						}
					}
				"
			)
		),
		'methods' => array(
			'subscribe' => array(
				'args' => array('subscriber', 'opts'),
				'body' => "
					var index = this.subscribers.indexOf(subscriber);
					if (index == -1) {
						this.options.push(opts);
						this.eventHandler.listen(subscriber.getElement(), 'click', onClick.bind(null, subscriber));
						this.subscribers.push(subscriber);
					} else extendOptions(index, opts);
				"
			),
			'unsubscribe' => array(
				'args' => array('subscriber'),
				'body' => "
					var idx = this.subscribers.indexOf(subscriber);
					if (idx > -1) {
						this.eventHandler.unlisten(subscriber.getElement(), 'click');
						this.subscribers.splice(idx, 1);
					}
				"
			),
			'dispose' => array(
				'body' => "
					this.subscribers = null;
					this.options = null;
					this.eventHandler.dispose();
					this.eventHandler = null;
				"
			)
		)
	);
?>