{{GLOBAL}}.set(function(o, p, n) {
	var a = isArray(n) ? n : [n];
	this.getKeys = function() {
		return a;
	};
	this.react = function(d) {
		if (isString(n)) o.update(d[n]);
		else o.update();
	};
	this.getOperator = function() {
		return o;
	};
	this.dispose = function() {
		n = o = p = null;
	};
}, 'OperatorUpdater');