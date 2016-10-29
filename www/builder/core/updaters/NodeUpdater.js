function NodeUpdater(n, p) {
	var a = isArray(p['pr']) ? p['pr'] : [p['pr']];
	this.getKeys = function() {
		return a;
	};
	this.react = function(d) {
		var c;
		if (isFunction(p['p'])) c = p['p'](); 
		else c = d[a[0]];
		n.textContent = c || '';
	};
	this.dispose = function() {
		n = p = a = null;
	};
}