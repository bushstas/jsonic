_c = new (function() {
	var ctrlist = {{CONTROLLERS}};
	this.get = function(id) {
		if (isString(ctrlist[id])) {
			ctrlist[id] = {{GLOBAL}}.get(ctrlist[id]);
		}
		if (isFunction(ctrlist[id])) {
			ctrlist[id] = new ctrlist[id]();
			{{GLOBAL}}.get('Core').initiate.call(ctrlist[id]);
		}
		return ctrlist[id];
	};
	this.load = function(ids) {
		var ctr;
		if (!isArray(ids)) ids = [ids];
		for (var i = 0; i < ids.length; i++) {
			ctr = this.get(ids[i]);
			if (isController(ctr)) {
				ctr.doAction(null, 'load');
			}
		}
	};
})();
{{GLOBAL}}.set(_c, 'Controllers');
var {{CONTROLLER}} = _c;