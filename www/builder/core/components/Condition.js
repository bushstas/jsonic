function Condition(params) {
	this.params = params;
	this.isTrue = !!this.params['i']();
}

Condition.prototype.render = function(parentElement, parentLevel) {
	this.parentElement = parentElement;
	this.parentLevel = parentLevel;
	this.createLevel(false);
};

Condition.prototype.createLevel = function(isUpdating) {
	this.level = new Level();
	this.level.setComponent(this.parentLevel.getComponent());
	var nextSiblingChild = isUpdating ? Core.getNextSiblingChild.call(this) : null;
	this.level.render(this.getChildren(), this.parentElement, this.parentLevel, nextSiblingChild);
};

Condition.prototype.update = function() {
	var isTrue = !!this.params['i']();
	if (isTrue != this.isTrue) {
		this.isTrue = isTrue;
		this.disposeLevel();
		this.createLevel(true);
	}
};

Condition.prototype.getChildren = function() {
	return this.isTrue ? this.params['c']() : (isFunction(this.params['e']) ? this.params['e']() : null);
};

Condition.prototype.disposeLevel = function() {
	if (this.level) {
		this.level.dispose();
		this.level = null;
	}
};

Condition.prototype.dispose = function() {
	Core.disposeLinks.call(this);
	this.disposeLevel();
	this.parentElement = null;
	this.parentLevel = null;
	this.params = null;
	this.nextSiblingChild = null;
};