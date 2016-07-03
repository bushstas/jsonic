function IfSwitch(params) {
	this.values = params['is'];
	this.default = params['d'];
	this.handler = params['c'];
	this.current = null;
	this.levels = [];
}

IfSwitch.prototype.createLevels = function(isUpdating) {
	var children = this.handler();
	var values = this.values();
	for (var i = 0; i < values.length; i++) {
		if (!!values[i]) {
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

IfSwitch.prototype.update = function(value) {
	this.value = value;
	this.disposeLevels();
	this.createLevels(true);
};

IfSwitch.prototype.dispose = function() {
	this.disposeLinks();
	this.disposeLevels();
	this.levels = null;
	this.parentElement = null;
	this.parentLevel = null;
	this.current = null;
	this.values = null;
	this.default = null;
	this.handler = null;
	this.nextSiblingChild = null;
	this.prevSiblingChild = null;
};