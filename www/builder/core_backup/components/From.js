{{GLOBAL}}.set({{COMPONENT}} = function(params) {
	this.levels = [];
	this.createLevels = function(isUpdating) {
		var p = (isFunction(params['p']) ? params['p']() : params['p']) || [];
		var a = ~~p[0], b = ~~p[1], s = ~~p[2] || 1;
		for (var i = a; i <= b; i += s) {
			this.createLevel(params['f'](i), isUpdating);
		}
	};
	this.update = function() {
		this.disposeLevels();
		this.createLevels(true);
	};
}, 'From');
