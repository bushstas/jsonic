_c = function(params) {
	this.params = params;
	this.isTrue = !!this.params['i']();
}
_p=_c.prototype;
_p.render = function(parentElement, parentLevel) {
	this.parentElement = parentElement;
	this.parentLevel = parentLevel;
	this.createLevel(false);
};
_p.createLevel = function(isUpdating) {
	this.level = new ({{GLOBAL}}.get('Level'))(this.parentLevel.getComponent());
	var nextSiblingChild = isUpdating ? {{GLOBAL}}.get('Core').getNextSiblingChild.call(this) : null;
	this.level.render(this.getChildren(), this.parentElement, this.parentLevel, nextSiblingChild);
};
_p.update = function() {
	var isTrue = !!this.params['i']();
	if (isTrue != this.isTrue) {
		this.isTrue = isTrue;
		this.disposeLevel();
		this.createLevel(true);
	}
};
_p.getChildren = function() {
	if (this.isTrue) return isFunction(this.params['c']) ? this.params['c']() : this.params['c'];
	return isFunction(this.params['e']) ? this.params['e']() : this.params['e'];
};
_p.disposeLevel = function() {
	if (this.level) {
		this.level.dispose();
		this.level = null;
	}
};
_p.dispose = function() {
	{{GLOBAL}}.get('Core').disposeLinks.call(this);
	this.disposeLevel();
	this.parentElement = null;
	this.parentLevel = null;
	this.params = null;
	this.nextSiblingChild = null;
};
{{GLOBAL}}.set(_c, 'Condition');