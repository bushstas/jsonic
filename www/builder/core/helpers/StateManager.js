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
	var s = function(g, c) {
		return gs(g||gv(c));
	};
	this.listen = function(c, g, k, h) {
		s(g,c).listen(c, k, h);
	};
	this.subscribe = function(c, g, k, h) {
		s(g,c).subscribe(c, k, h);
	};
	this.dispatchEvent = function(c, g, n, p) {
		s(g,c).dispatchEvent(n, p);
	};
	this.set = function(c, g, n, v) {
		s(g,c).set(n, v);
	};
	this.get = function(c, g, n) {
		return s(g,c).get(n);
	};
	this.createUpdater = function(u, c, a, p, g) {
		s(g,c).createUpdater(u, c, a, p, p[g ? 'gl' : 'lc']);
	};
}
StateManager = new StateManager();