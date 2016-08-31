function Control() {
	if (this !== window) return;
	var onChangeChildControl = function(e) {
		this.dispatchChange();
	};

	Control.prototype.onChange = function(e) {};

	Control.prototype.dispatchChange = function() {		
		var params = this.getChangeEventParams();
		this.onChange(params);
		this.dispatchEvent('change', params);
	};

	Control.prototype.getChangeEventParams = function() {
		return {value: this.getValue()};
	};

	Control.prototype.getInitials = function() {
		return {'enabled': true};
	};

	Control.prototype.registerControl = function(control, name) {
		Component.prototype.registerControl.call(this, control, name);
	 	this.addListener(control, 'change', onChangeChildControl.bind(this));
	};

	Control.prototype.setName = function(name) {
		this.name = name;
	};

	Control.prototype.getName = function() {
		return this.name;
	};

	Control.prototype.getValue = function() {
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
	};

	Control.prototype.getControlValue = function() {
		return this.get('value');
	};

	Control.prototype.getProperValue = function(value) {
		return value;
	};

	Control.prototype.setValue = function(value, fireChange) {
		if (this.hasControls()) {
			this.setControlsData(value);
		} 
		this.setControlValue(value);
		if (fireChange) this.dispatchChange();
	};

	Control.prototype.setControlValue = function(value) {
		this.set('value', value);
	};

	Control.prototype.isEnabled = function() {
		return !!this.get('enabled');
	};

	Control.prototype.setEnabled = function(isEnabled) {
		this.set('enabled', isEnabled);
	};

	Control.prototype.clear = function(fireChange) {
		this.clearControl();
		if (fireChange) this.dispatchChange();
	};

	Control.prototype.clearControl = function() {
		this.setControlValue('');
	};
	
	Control.prototype.disposeInternal = function() {
		this.controls = null;
		this.options = null;
	};
}
Control();