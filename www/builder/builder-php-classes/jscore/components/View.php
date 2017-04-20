<?php

	$data = array(
		'name' => 'View',
		'condition' => '!this||this==window',
		'methods' => array(
			'onRenderComplete' => array(
				'body' => "
					this.dispatchReadyEvent();
				"
			),
			'setOnReadyHandler' => array(
				'args' => array('handler'),
				'body' => "
					this.onReadyHandler = handler;
				"
			),
			'dispatchReadyEvent' => array(
				'args' => array(''),
				'body' => "
					if (isFunction(this.onReadyHandler)) {
						this.onReadyHandler();
					}
					this.onReady();
				"
			),
			'activate' => array(
				'args' => array('isActivated'),
				'body' => "
					if (isActivated) {
						this.dispatchReadyEvent();
					}
				"
			),
			'getTitleParams' => array(),
			'onReady' => array()
		)
	);
?>