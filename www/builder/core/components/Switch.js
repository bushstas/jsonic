function Switch(params) {
	this.params = params;
	this.levels = [];

	this.createLevels = function(isUpdating) {
		var p = this.params['sw']();
		var v = p[0], vs = p[1], ch = p[2], d = p[3];
		if (!isArray(vs)) {
			vs = [vs]; ch = [ch];
		}
		for (var i = 0; i < vs.length; i++) {
			if (v === vs[i]) {
				for (var j = 0; j < ch[i].length; j++) this.createLevel(ch[i][j], isUpdating);
				return;
			}
		}
		if (!isUndefined(d)) this.createLevel(d, isUpdating);
	};

	this.dispose = function() {
		this.disposeLinks();
		this.disposeLevels();
		this.levels = null;
		this.parentElement = null;
		this.parentLevel = null;
		this.params = null
		this.nextSiblingChild = null;
		this.prevSiblingChild = null;
	};
}

Switch.prototype.update = function(value) {
	this.value = value;
	this.disposeLevels();
	this.createLevels(true);
};

