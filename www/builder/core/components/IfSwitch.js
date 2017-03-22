{{GLOBAL}}.set({{COMPONENT}} = function(params) {
	var cur = null;
	this.levels = [];
	isChanged();
	function isChanged() {
		var v = params['is']()['is'], c = cur;
		for (var i = 0; i < v.length; i++) {
			if (!!v[i]) {
				cur = i;
				return i !== c;
			}
		}
		cur = null;
		return c !== null;
	}
	this.createLevels = function(isUpdating) {
		var p = params['is']();
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
		this.current = null;
		this.nextSiblingChild = null;
		this.prevSiblingChild = null;
	};
}, 'IfSwitch');