function Control() {
	var getCorrectedValue = function(value) {
		var type = Objects.get(this.options, 'type');
		switch (type) {
			case 'array':
				if (isString(value)) {
					value = value.split(',');
				} else if (!isArray(value)) {
					value = [value];
				}
			break;
			case 'string':
				if (isArray(value)) {
					value = value.join(',');
				} else if (isObject(value)) {
					value = JSON.stringify(value);
				} else if (isNumber(value)) {
					value = value + '';
				} else if (!!value) {
					value = '1';
				} else {
					value = '';
				}
			break;
			case 'number':
				if (isString(value)) {
					value = stringToNumber(value);
				} else if (isBool(value)) {
					value = !!value ? 1 : 0;
				} else if (isArray(value)) {
					value = ~~value[0];
				} else {
					value = 0;
				}
			break;
			case 'boolean':
				value = !!value && value === '0';
			break;
		}
		return this.getProperValue(value);
	};
	var getControlValue = function() {
		return (!isUndefined(this.value) ? this.value : this.props.value) || '';
	};
	var dispatchChange = function() {
		this.dispatchEvent('change', {'value': this.get('value'), 'instance': this});
	};
	var onChangeChildControl = function(e) {
		dispatchChange.call(this);
	};
	Control.prototype.getInitials = function() {
		return {'enabled': true};
	};
	Control.prototype.attachControl = function(control) {
		if (isString(control)) control = this.getChildById(control);
		if (isControl(control)) {
			this.controls = this.controls || {};
			this.controls[control.getId()] = control;
			this.addListener(control, 'change', onChangeChildControl.call(this));
		}
	};
	Control.prototype.getName = function() {
		return this.props.name;
	};
	Control.prototype.getValue = function() {
		var value;
		if (this.hasControls()) {
			value = {};
			for (var k in this.controls) value[k] = this.controls[k].getValue();
		} else value = getCorrectedValue.call(this, getControlValue.call(this));
		return value;
	};
	Control.prototype.getProperValue = function(value) {
		return value;
	};
	Control.prototype.setValue = function(value) {
		if (this.hasControls() && isObject(value)) {
			for (var k in value) {
				if (isControl(this.controls[k])) this.controls[k].setValue(this.controls[k].hasControls() ? value : value[k]);
			}
		} else {
			this.value = value;
			this.setProperValue(value);
		}
	};
	Control.prototype.setProperValue = function(value) {
		this.set('value', value);
	};
	Control.prototype.hasControls = function() {
		return !Objects.empty(this.controls);
	}
	Control.prototype.isEnabled = function() {
		return !!this.get('enabled');
	};
	Control.prototype.setEnabled = function(isEnabled) {
		this.set('enabled', isEnabled);
	};
	Control.prototype.disposeInternal = function() {
		this.controls = null;
		this.options = null;
		this.value = null;
	};
}
Control();