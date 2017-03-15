{{GLOBAL}}.set({{COMPONENT}} = function(params) {
	this.conditions = params['is'];
	this.default = params['d'];
	this.children = params['c'];
	this.current = null;
	this.levels = [];

	this.createLevels = function(isUpdating) {
		var children = this.children;
		var conditions = this.conditions, c;
		for (var i = 0; i < conditions.length; i++) {
			c = conditions[i];
			if (isFunction(c)) c = c();
			if (!!c) {
				if (i === this.current) return;
				for (var j = 0; j < children[i].length; j++) this.createLevel(children[i][j], isUpdating);
				this.current = i;
				return;
			}
		}
		if (isArray(this.default)) {
			for (i = 0; i < this.default.length; i++) this.createLevel(this.default[i], isUpdating);
		}
	};

	this.dispose = function() {
		this.disposeLinks();
		this.disposeLevels();
		this.levels = null;
		this.parentElement = null;
		this.parentLevel = null;
		this.current = null;
		this.conditions = null;
		this.default = null;
		this.children = null;
		this.nextSiblingChild = null;
		this.prevSiblingChild = null;
	};
}, 'IfSwitch');
{{PROTO}}={{COMPONENT}}.prototype;
{{PROTO}}.update = function(value) {
	this.value = value;
	this.disposeLevels();
	this.createLevels(true);
};