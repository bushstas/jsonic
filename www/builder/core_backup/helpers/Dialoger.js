var {{DIALOGER}};
{{GLOBAL}}.set({{DIALOGER}} = new (function() {
	var ds = {};
	var cid, dc, d, opts;
	this.show = function(c, options) {
		if (isString(c)) c = {{GLOBAL}}.get(c);
		if (isFunction(c)) {
			var id;
			if (isObject(options)) {
				id = options['did'];
			}
			opts = options;
			defineId(c, id);
			defineDialog();
			showDialog();
		}
	};
	this.hide = function(c, id) {
		defineId(c, id);
		if (ds[cid]) ds[cid].close();
	};
	this.get = function(c, id) {
		defineId(c, id);
		return ds[cid];
	};
	this.expand = function(c, id) {
		defineId(c, id);
		if (ds[cid]) ds[cid].expand(true);
	};
	this.minimize = function(c, id) {
		defineId(c, id);
		if (ds[cid]) ds[cid].expand(false);
	};
	this.dispose = function(c, id) {
		defineId(c, id);
		if (ds[cid]) ds[cid].dispose();
		delete ds[cid];
	};
	var defineId = function(c, id) {
		dc = c;
		if (!isFunction(c)) return '_';
		cid = c.name + (isPrimitive(id) ? '_' + id : '');
	};
	var defineDialog = function() {
		if (isUndefined(ds[cid])) {
			ds[cid] = new dc();
			{{GLOBAL}}.get('Core').initiate.call(ds[cid]);
			ds[cid].render(document.body);
		}
		d = ds[cid];
	};
	var showDialog = function() {
		if (isObject(opts)) {
			d.set(opts);
		}
		d.show();
	};
	var closeAll = function() {
		for (var k in ds) {
			ds[k].hide();
		}
	};
	window.addEventListener('popstate', closeAll);
})(), 'Dialoger');