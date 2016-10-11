function Corrector() {
	var correctorsList = {{CORRECTORS}};
	var corrs = {};
	if (typeof correctorsList != 'undefined' && isArray(correctorsList)) {
		for (var i = 0; i < correctorsList.length; i++) {
			if (isFunction(correctorsList[i])) corrs[correctorsList[i].name.replace(/Crr$/, '')] = correctorsList[i];
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