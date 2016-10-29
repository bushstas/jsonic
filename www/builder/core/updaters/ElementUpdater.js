function ElementUpdater(e, p) {
	this.getKeys = function() {
		var a = [];
		for (k in p['n']) {
			if (isString(p['n'][k])) a.push(p['n'][k]);
			else a.push.apply(a, p['n'][k]);
		}
		return a;
	};
	this.react = function(d) {
		var n, k, i, pn;
		for (k in p['n']) {
			pn = p['n'][k];			
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