function Validator() {
	this.assert = function(v,m,e) {
		if (!m(v)) console.log(e);
		return v;
	};
}
Validator = new Validator();