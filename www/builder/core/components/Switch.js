function Switch(params) {
	this.value = params['sw'];
	this.values = params['s'];
	this.default = params['d'];
	this.handler = params['c'];
	this.levels = [];
}

Switch.prototype.createLevels = function(isUpdating) {
	var children = this.handler();
	for (var i = 0; i < this.values.length; i++) {
		if (this.value === this.values[i]) {
			for (var j = 0; j < children[i].length; j++) {
				this.createLevel(children[i][j], isUpdating);
			}
			return;
		}
	}
	if (isArray(this.default)) {
		for (i = 0; i < this.default.length; i++) this.createLevel(this.default[i], isUpdating);
	}
};

Switch.prototype.update = function(value) {
	this.value = value;
	this.disposeLevels();
	this.createLevels(true);
};

Switch.prototype.dispose = function() {
	this.disposeLinks();
	this.disposeLevels();
	this.levels = null;
	this.parentElement = null;
	this.parentLevel = null;
	this.value = null
	this.values = null;
	this.default = null;
	this.handler = null;
	this.nextSiblingChild = null;
	this.prevSiblingChild = null;
};