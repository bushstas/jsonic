{{GLOBAL}}.set(function() {
	this.assert = function(v,m,e) {
		if (!m(v)) console.log(e);
		return v;
	};
}, 'Validator');