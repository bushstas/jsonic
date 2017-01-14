function Condition(params) {
	this.params = params;
	this.isTrue = !!this.params['i']();
}
var p = Condition.prototype;
p.render = function(parentElement, parentLevel) {
	this.parentElement = parentElement;
	this.parentLevel = parentLevel;
	this.createLevel(false);
};
p.createLevel = function(isUpdating) {
	this.level = new Level(this.parentLevel.getComponent());
	var nextSiblingChild = isUpdating ? Core.getNextSiblingChild.call(this) : null;
	this.level.render(this.getChildren(), this.parentElement, this.parentLevel, nextSiblingChild);
};
p.update = function() {
	var isTrue = !!this.params['i']();
	if (isTrue != this.isTrue) {
		this.isTrue = isTrue;
		this.disposeLevel();
		this.createLevel(true);
	}
};
p.getChildren = function() {
	if (this.isTrue) return isFunction(this.params['c']) ? this.params['c']() : this.params['c'];
	return isFunction(this.params['e']) ? this.params['e']() : this.params['e'];
};
p.disposeLevel = function() {
	if (this.level) {
		this.level.dispose();
		this.level = null;
	}
};
p.dispose = function() {
	Core.disposeLinks.call(this);
	this.disposeLevel();
	this.parentElement = null;
	this.parentLevel = null;
	this.params = null;
	this.nextSiblingChild = null;
};