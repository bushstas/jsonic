function NodeUpdater(n, p) {
	var a = isArray(p['n']) ? p['n'] : [p['n']];
	this.getKeys = function() {
		return a;
	};
	this.react = function(d) {
		var c;
		if (isFunction(p['v'])) c = p['v'](); 
		else c = d[a[0]];
		n.textContent = c || '';
	};
	this.dispose = function() {
		n = p = a = null;
	};
}