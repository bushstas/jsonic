{{GLOBAL}}.set(({{COMPONENT}} = function() {
	if (!this || this == window) {
		var onChangeChildControl = function(e) {
			this.dispatchChange();
		};
		{{PROTO}}={{COMPONENT}}.prototype;
		{{PROTO}}.onChange = function(e) {};

		{{PROTO}}.dispatchChange = function() {		
			var params = this.getChangeEventParams();
			this.onChange(params);
			this.dispatchEvent('change', params);
		};
		{{PROTO}}.getChangeEventParams = function() {
			return {value: this.getValue()};
		};
		{{PROTO}}.initiate = function() {
			this.preset('enabled', true);
		};
		{{PROTO}}.registerControl = function(control, name) {
			{{GLOBAL}}.get('Component').prototype.registerControl.call(this, control, name);
		 	this.addListener(control, 'change', onChangeChildControl.bind(this));
		};
		{{PROTO}}.setName = function(name) {
			this.name = name;
		};
		{{PROTO}}.getName = function() {
			return this.name;
		};
		{{PROTO}}.getValue = function() {
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
		{{PROTO}}.getControlValue = function() {
			return this.get('value');
		};
		{{PROTO}}.getProperValue = function(value) {
			return value;
		};
		{{PROTO}}.setValue = function(value, fireChange) {
			if (this.hasControls()) {
				this.setControlsData(value);
			} 
			this.setControlValue(value);
			if (fireChange) this.dispatchChange();
		};
		{{PROTO}}.setControlValue = function(value) {
			this.set('value', value);
		};
		{{PROTO}}.isEnabled = function() {
			return !!this.get('enabled');
		};
		{{PROTO}}.setEnabled = function(isEnabled) {
			this.set('enabled', isEnabled);
		};
		{{PROTO}}.clear = function(fireChange) {
			this.clearControl();
			if (fireChange) this.dispatchChange();
		};
		{{PROTO}}.clearControl = function() {
			this.setControlValue('');
		};		
		{{PROTO}}.disposeInternal = function() {
			this.controls = null;
			this.options = null;
		};
	}
	return {{COMPONENT}};
})(), 'Control');