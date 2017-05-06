{{GLOBAL}}.set({{COMPONENT}} = function(params) {
	var cur = null;
	this.levels = [];
	isChanged();
	function isChanged() {
		var p = params['sw']();
		var v = p['sw'], vs = p['cs'], c = cur;
		if (!isUndefined(vs)) {
			if (!isArray(vs)) vs = [vs];
			for (var i = 0; i < vs.length; i++) {
				if (v === vs[i]) {
					cur = i;
					return i !== c;
				}
			}
		}
		cur = null;
		return c !== null;
	}
	this.createLevels = function(isUpdating) {
		var p = params['sw']();
		var c = p['c'], d = p['d'];
		if (cur !== null) {
			this.createLevel(c[cur], isUpdating);
		} else if (!isUndefined(d)) {
			this.createLevel(d, isUpdating);
		}
	};
	this.update = function() {
		if (isChanged()) {
			this.disposeLevels();
			this.createLevels(true);
		}
	};
	this.dispose = function() {
		this.disposeLinks();
		this.disposeLevels();
		this.levels = null;
		this.parentElement = null;
		this.parentLevel = null;
		this.nextSiblingChild = null;
		this.prevSiblingChild = null;
		params = null;
	};
}, 'Switch');
