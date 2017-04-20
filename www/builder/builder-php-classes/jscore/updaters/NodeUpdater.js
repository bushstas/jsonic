{{GLOBAL}}.set(function(n, p, pn) {
	var a = isArray(pn) ? pn : [pn];
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
}, 'NodeUpdater');