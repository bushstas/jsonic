function ComponentUpdater(c, p) {
	this.getKeys = function() {
		var a = [];
		for (var k in p['n']) {
			if (a.indexOf(p['n'][k]) == -1) {
				if (isString(p['n'][k])) a.push(p['n'][k]);
				else a.push.apply(a, p['n'][k]);
			}
		}
		return a;
	};
	this.react = function(d) {
		var pp = p['p'](), cp = {};
		var pc = !!p['n']['props'];
		if (pc && isObject(pp['p'])) {
			cp =  pp['p'];
		}
		for (var k in p['n']) {
			if (isString(p['n'][k]) && !isUndefined(d[p['n'][k]])) {
				cp[k] = pc && pp['ap'] ? pp['ap'][k] : pp['p'][k];
			}
		}
		c.set(cp);
	};
	this.dispose = function() {
		c = p = null;
	};
}