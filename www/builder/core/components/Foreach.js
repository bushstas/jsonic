function Foreach(params) {
	this.items = params['p'];
	this.handler = params['h'];
	this.levels = [];
}

Foreach.prototype.render = function(parentElement, parentLevel) {
	this.parentElement = parentElement;
	this.parentLevel = parentLevel;
	this.createLevels(false);
};

Foreach.prototype.createLevels = function(isUpdating) {
	if (this.items instanceof Array) {
		for (var i = 0; i < this.items.length; i++) {
			this.createLevel(this.handler(this.items[i], i), isUpdating);
		}
	}
};

Foreach.prototype.createLevel = function(items, isUpdating) {
	var level = new Level();
	var nextSiblingChild = isUpdating ? this.getNextSiblingChild() : null;
	level.render(items, this.parentElement, this.parentLevel, nextSiblingChild);
	this.levels.push(level);
};

Foreach.prototype.update = function(items) {
	this.items = items;
	this.disposeLevels();
	this.createLevels(true);
};

Foreach.prototype.propagatePropertyChange = function(propName, propValue) {
	for (var i = 0; i < this.levels.length; i++) {
		this.levels[i].propagatePropertyChange(propName, propValue);
	}
};

Foreach.prototype.getFirstNodeChild = function() {
	if (this.levels[0]) {
		return this.levels[0].getFirstNodeChild();
	}
	return null;
};

Foreach.prototype.disposeLevels = function() {
	for (var i = 0; i < this.levels.length; i++) {
		this.levels[i].dispose();
	}
	this.levels = [];
};

Foreach.prototype.dispose = function() {
	this.disposeLinks();
	this.disposeLevels();
	this.levels = null;
	this.parentElement = null;
	this.parentLevel = null;
	this.items = null
	this.handler = null;
	this.nextSiblingChild = null;
	this.prevSiblingChild = null;
};