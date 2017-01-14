function Control() {
	if (this !== window) return;
	var onChangeChildControl = function(e) {
		this.dispatchChange();
	};
	var p = Control.prototype;
	p.onChange = function(e) {};

	p.dispatchChange = function() {		
		var params = this.getChangeEventParams();
		this.onChange(params);
		this.dispatchEvent('change', params);
	};

	p.getChangeEventParams = function() {
		return {value: this.getValue()};
	};

	p.getInitials = function() {
		return {'enabled': true};
	};

	p.registerControl = function(control, name) {
		Component.prototype.registerControl.call(this, control, name);
	 	this.addListener(control, 'change', onChangeChildControl.bind(this));
	};

	p.setName = function(name) {
		this.name = name;
	};

	p.getName = function() {
		return this.name;
	};

	p.getValue = function() {
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

	p.getControlValue = function() {
		return this.get('value');
	};

	p.getProperValue = function(value) {
		return value;
	};

	p.setValue = function(value, fireChange) {
		if (this.hasControls()) {
			this.setControlsData(value);
		} 
		this.setControlValue(value);
		if (fireChange) this.dispatchChange();
	};

	p.setControlValue = function(value) {
		this.set('value', value);
	};

	p.isEnabled = function() {
		return !!this.get('enabled');
	};

	p.setEnabled = function(isEnabled) {
		this.set('enabled', isEnabled);
	};

	p.clear = function(fireChange) {
		this.clearControl();
		if (fireChange) this.dispatchChange();
	};

	p.clearControl = function() {
		this.setControlValue('');
	};
	
	p.disposeInternal = function() {
		this.controls = null;
		this.options = null;
	};
}
Control();