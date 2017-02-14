_c = function() {
	if (this) return;
	var onChangeChildControl = function(e) {
		this.dispatchChange();
	};
	_p.onChange = function(e) {};

	_p.dispatchChange = function() {		
		var params = this.getChangeEventParams();
		this.onChange(params);
		this.dispatchEvent('change', params);
	};

	_p.getChangeEventParams = function() {
		return {value: this.getValue()};
	};

	_p.initiate = function() {
		this.preset('enabled', true);
	};

	_p.registerControl = function(control, name) {
		{{GLOBAL}}.get('Component').prototype.registerControl.call(this, control, name);
	 	this.addListener(control, 'change', onChangeChildControl.bind(this));
	};

	_p.setName = function(name) {
		this.name = name;
	};

	_p.getName = function() {
		return this.name;
	};

	_p.getValue = function() {
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

	_p.getControlValue = function() {
		return this.get('value');
	};

	_p.getProperValue = function(value) {
		return value;
	};

	_p.setValue = function(value, fireChange) {
		if (this.hasControls()) {
			this.setControlsData(value);
		} 
		this.setControlValue(value);
		if (fireChange) this.dispatchChange();
	};

	_p.setControlValue = function(value) {
		this.set('value', value);
	};

	_p.isEnabled = function() {
		return !!this.get('enabled');
	};

	_p.setEnabled = function(isEnabled) {
		this.set('enabled', isEnabled);
	};

	_p.clear = function(fireChange) {
		this.clearControl();
		if (fireChange) this.dispatchChange();
	};

	_p.clearControl = function() {
		this.setControlValue('');
	};
	
	_p.disposeInternal = function() {
		this.controls = null;
		this.options = null;
	};
}
_p=_c.prototype;_c();{{GLOBAL}}.set(_c, 'Control');