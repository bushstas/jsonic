function OperatorUpdater(o, p) {
	var n = o instanceof Foreach ? p['n'] : p['p'];
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
}