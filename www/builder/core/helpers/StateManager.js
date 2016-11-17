function StateManager() {
	var pvc = {{PARENTALVIEWCNT}};
	var global;
	var states = {};
	var links = {};
	var gv = function(c) {
		var u = c.getUniqueId();
		return links[u] = links[u] || gl(c);
	};
	var gl = function(c) {
		var p = c.getElement().getAncestor('.' + pvc);
		return p.getData('name');
	};
	var gs = function(v) {
		if (isString(v)) return states[v] = states[v] || new State();
		else return global = global || new State();
	};
	this.listen = function(c, g, k, h) {
		gs(g||gv(c)).listen(c, k, h);
	};
	this.subscribe = function(c, g, k, h) {
		gs(g||gv(c)).subscribe(c, k, h);
	};
	this.dispatchEvent = function(c, g, n, p) {
		gs(g||gv(c)).dispatchEvent(n, p);
	};
	this.set = function(c, g, n, v) {
		gs(g||gv(c)).set(n, v);
	};
	this.get = function(c, g, n) {
		return gs(g||gv(c)).get(n);
	};
}
StateManager = new StateManager();