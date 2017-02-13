_c = function(e, p, n) {
	this.getKeys = function() {
		var a = [];
		for (k in n) {
			if (isString(n[k])) a.push(n[k]);
			else a.push.apply(a, n[k]);
		}
		return a;
	};
	this.react = function(d) {
		var k, i, pn;
		for (k in n) {
			pn = n[k];
			if (isString(pn)) pn = [pn];
			for (i = 0; i < pn.length; i++) {
				if (!isUndefined(d[pn[i]])) {
					e.attr({{ATTRIBUTES}}[k] || k, p['p']()[k] || '');
					break;
				}
			}
		}
	};
	this.dispose = function() {
		e = p = null;
	};
}
{{GLOBAL}}.set(_c, 'ElementUpdater');