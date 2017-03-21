{{GLOBAL}}.set({{COMPONENT}} = function(params) {
	this.levels = [];

	this.createLevels = function(isUpdating) {
		this.createLevel(params['l'](), isUpdating);
	};

	this.dispose = function() {
		this.disposeLinks();
		this.disposeLevels();
		this.levels = null;
		this.parentElement = null;
		this.parentLevel = null;
		this.nextSiblingChild = null;
		this.prevSiblingChild = null;
		params = null;
	};
}, 'Let');
{{PROTO}}={{COMPONENT}}.prototype;
{{PROTO}}.update = function() {
	this.disposeLevels();
	this.createLevels(true);
};