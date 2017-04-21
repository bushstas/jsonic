<?php

	$data = array(
		'name' => 'Menu',
		'condition' => '!this||this==window',
		'methods' => array(
			'onRenderComplete' => array(
				'body' => "
					var router = ".CONST_GLOBAL.".get('Router');
					if (router.hasMenu(this)) {
						this.onNavigate(router.getCurrentRouteName());
					}
				"
			),
			'onNavigate' => array(
				'args' => array('viewName'),
				'body' => "
					if (this.rendered) {
						if (isElement(this.activeButton)) {
							this.setButtonActive(this.activeButton, false);	
						}
						var button = this.getButton(viewName);
						if (isElement(button)) {
							this.setButtonActive(button, true);
						}
					}
				"
			),
			'getButton' => array(
				'args' => array('viewName'),
				'body' => "
					return this.findElement('a[role=\"' + viewName + '\"]');
				"
			),
			'setButtonActive' => array(
				'args' => array('button', 'isActive'),
				'body' => "
					var activeClassName = this.activeButtonClass || '->> active';
					button.toggleClass(activeClassName, isActive);
					if (isActive) {
						this.activeButton = button;
					}
				"
			),
			'disposeInternal' => array(
				'body' => "
					this.activeButton = null;
				"
			)
		)
	);
?>