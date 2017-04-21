<?php

	$data = array(
		'name' => 'Control',
		'condition' => '!this||this==window',
		'privateMethods' => array(
			'onChangeChildControl' => array(
				'args' => array('e'),
				'body' => "
					this.dispatchChange();
				"
			)
		),
		'methods' => array(
			'initiate' => array(
				'body' => "
					this.preset('enabled', true);
				"
			),
			'onChange' => array(
				'args' => array('e'),
				'body' => ""
			),
			'dispatchChange' => array(
				'body' => "
					var params = this.getChangeEventParams();
					this.onChange(params);
					this.dispatchEvent('change', params);
				"
			),
			'getChangeEventParams' => array(
				'body' => "
					return {value: this.getValue()};
				"
			),
			'registerControl' => array(
				'args' => array('control', 'name'),
				'body' => "
					".CONST_GLOBAL.".get('Component').prototype.registerControl.call(this, control, name);
		 			this.addListener(control, 'change', onChangeChildControl.bind(this));
				"
			),
			'setName' => array(
				'args' => array('name'),
				'body' => "
					this.name = name;
				"
			),
			'getName' => array(
				'body' => "
					return this.name;
				"
			),
			'getValue' => array(
				'body' => "
					var value;
					if (this.hasControls()) {
						value = {};
						for (var k in this.controls) {
							if (isArray(this.controls[k])) {
								value[k] = [];
								for (var i = 0; i < this.controls[k].length; i++) value[k].push(this.controls[k][i].getValue());
							} else value[k] = this.controls[k].getValue();
						}
					} else value = this.getControlValue();
					return value;
				"
			),
			'getControlValue' => array(
				'body' => "
					return this.get('value');
				"
			),
			'getProperValue' => array(
				'args' => array('value'),
				'body' => "
					return value;
				"
			),
			'setValue' => array(
				'args' => array('value', 'fireChange'),
				'body' => "
					if (this.hasControls()) {
						this.setControlsData(value);
					} 
					this.setControlValue(value);
					if (fireChange) this.dispatchChange();
				"
			),
			'setControlValue' => array(
				'args' => array('value'),
				'body' => "
					this.set('value', value);
				"
			),
			'isEnabled' => array(
				'body' => "
					return !!this.get('enabled');
				"
			),
			'setEnabled' => array(
				'args' => array('isEnabled'),
				'body' => "
					this.set('enabled', isEnabled);
				"
			),
			'clear' => array(
				'args' => array('fireChange'),
				'body' => "
					this.clearControl();
					if (fireChange) this.dispatchChange();
				"
			),
			'clearControl' => array(
				'body' => "
					this.setControlValue('');
				"
			),
			'disposeInternal' => array(
				'body' => "
					this.controls = null;
					this.options = null;
				"
			)
		)
	);
?>