<?php

	$data = array(
		'mode' => 4,
		'prototypeOf' => 'MouseEvent',
		'methods' => array(
			'getTarget' => array(
				'args' => array('selector'),
				'body' => "
					return this.target.getAncestor(selector);
				"
			),
			'getTargetData' => array(
				'args' => array('selector', 'dataAttr'),
				'body' => "
					var target = this.getTarget(selector);
					return !!target ? target.getData(dataAttr) : '';
				"
			),
			'targetHasAncestor' => array(
				'args' => array('element'),
				'body' => "
					if (isElement(element)) {
						var target = this.target;
						while (target) {
							if (target == element) {
								return true;
							}
							target = target.parentNode;
						}
					}
					return false;
				"
			),
			'targetHasClass' => array(
				'args' => array('className'),
				'body' => "
					return this.target.hasClass(className) || (!!this.target.parentNode && this.target.parentNode.hasClass(className));
				"
			),
			'getTargetWithClass' => array(
				'args' => array('className', 'strict'),
				'body' => "
					if (this.target.hasClass(className)) return this.target;
					if (!strict || !this.target.className) {
						if (!!this.target.parentNode && this.target.parentNode.hasClass(className)) return this.target.parentNode;
					}
					return null;
				"
			)
		)
	);
?>