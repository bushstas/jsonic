{{GLOBAL}}.set({{COMPONENT}} = function(params) {
	this.levels = [];

	this.createLevels = function(isUpdating) {
		var p = params['sw']();
		var v = p['sw'], vs = p['cs'], ch = p['c'], d = p['d'];
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
		this.nextSiblingChild = null;
		this.prevSiblingChild = null;
		params = null;
	};
}, 'Switch');
{{PROTO}}={{COMPONENT}}.prototype;
{{PROTO}}.update = function() {
	this.disposeLevels();
	this.createLevels(true);
};