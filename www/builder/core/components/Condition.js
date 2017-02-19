{{COMPONENT}} = function(params) {
	this.params = params;
	this.isTrue = !!this.params['i']();
}
{{PROTO}}={{COMPONENT}}.prototype;
{{PROTO}}.render = function(parentElement, parentLevel) {
	this.parentElement = parentElement;
	this.parentLevel = parentLevel;
	this.createLevel(false);
};
{{PROTO}}.createLevel = function(isUpdating) {
	this.level = new ({{GLOBAL}}.get('Level'))(this.parentLevel.getComponent());
	var nextSiblingChild = isUpdating ? {{GLOBAL}}.get('Core').getNextSiblingChild.call(this) : null;
	this.level.render(this.getChildren(), this.parentElement, this.parentLevel, nextSiblingChild);
};
{{PROTO}}.update = function() {
	var isTrue = !!this.params['i']();
	if (isTrue != this.isTrue) {
		this.isTrue = isTrue;
		this.disposeLevel();
		this.createLevel(true);
	}
};
{{PROTO}}.getChildren = function() {
	if (this.isTrue) return isFunction(this.params['c']) ? this.params['c']() : this.params['c'];
	return isFunction(this.params['e']) ? this.params['e']() : this.params['e'];
};
{{PROTO}}.disposeLevel = function() {
	if (this.level) {
		this.level.dispose();
		this.level = null;
	}
};
{{PROTO}}.dispose = function() {
	{{GLOBAL}}.get('Core').disposeLinks.call(this);
	this.disposeLevel();
	this.parentElement = null;
	this.parentLevel = null;
	this.params = null;
	this.nextSiblingChild = null;
};
{{GLOBAL}}.set({{COMPONENT}}, 'Condition');