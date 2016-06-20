function Control() {}
Control.prototype.initiateInternal = function() {};
Control.prototype.checkInitials = function() {
	var initials = this.getInitials();
	for (var k in initials) {
		if (initials[k] && isObject(initials[k])) {
			if (k == 'options') {
				this.options = initials[k];
			}
		}
	}	
};
Control.prototype.getName = function() {
	return this.props.name;
};
Control.prototype.getValue = function() {
	var value = (!isUndefined(this.value) ? this.value : this.props.value) || '';
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
Control.prototype.getProperValue = function(value) {
	return value;
};
Control.prototype.setValue = function(value) {
	this.value = value;
	this.setProperValue(value);
};
Control.prototype.setProperValue = function(value) {
	this.set('value', value);
};
Control.prototype.onChange = function(e) {
	this.value = e.target.value;
};
Control.prototype.disposeInternal = function() {
	this.options = null;
	this.value = null;
};