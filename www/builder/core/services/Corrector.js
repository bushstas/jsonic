function Corrector() {
	var corrs = {};
	if (typeof __CRRS != 'undefined' && isArray(__CRRS)) {
		for (var i = 0; i < __CRRS.length; i++) {
			if (isFunction(__CRRS[i])) corrs[__CRRS[i].name.replace(/Crr$/, '')] = __CRRS[i];
		}
	}
	this.correct = function(corrName, data) {
		if (isFunction(corrs[corrName])) {
			corrs[corrName] = new corrs[corrName]();			
		}
		if (isObject(corrs[corrName])) return corrs[corrName].correct(data);
		else return data;
	};
}
Corrector = new Corrector();