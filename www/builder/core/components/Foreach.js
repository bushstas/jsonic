function Foreach(params) {
	this.getItems = params['p'];
	this.items = params['p']();
	this.handler = params['h'];
	this.isRight = !!params['r'];
	this.levels = [];
}

Foreach.prototype.render = function(parentElement, parentLevel) {
	this.parentElement = parentElement;
	this.parentLevel = parentLevel;
	this.createLevels(false);
};

Foreach.prototype.createLevels = function(isUpdating) {
	if (isArray(this.items)) {
		if (!this.isRight) {
			for (var i = 0; i < this.items.length; i++) {
				this.createLevel(this.handler(this.items[i], i), isUpdating);
			}
		} else {
			for (var i = this.items.length - 1; i >= 0; i--) {
				this.createLevel(this.handler(this.items[i], i), isUpdating);
			}
		}
	} else if (isObject(this.items)) {
		if (!this.isRight) {
			for (var k in this.items) {
				this.createLevel(this.handler(this.items[k], k), isUpdating);
			}
		} else {
			var keys = Objects.getKeys(this.items);
			keys.reverse();
			for (var i = 0; i < keys.length; i++) {
				this.createLevel(this.handler(this.items[keys[i]], keys[i]), isUpdating);
			}
		}
	}
};

Foreach.prototype.createLevel = function(items, isUpdating, index) {
	var level = new Level();
	level.setComponent(this.parentLevel.getComponent());
	var nextSiblingChild;
	if (isNumber(index) && this.levels[index]) {
		nextSiblingChild = this.levels[index].getFirstNodeChild();
	} else {
		nextSiblingChild = isUpdating ? Core.getNextSiblingChild.call(this) : null;
	}
	level.render(items, this.parentElement, this.parentLevel, nextSiblingChild);
	this.levels.insertAt(level, index);
};

Foreach.prototype.update = function(items) {
	this.items = this.getItems();
	this.disposeLevels();
	this.createLevels(true);
};

Foreach.prototype.add = function(item, index) {
	this.createLevel(this.handler(item, ~~index), false, index);	
};

Foreach.prototype.remove = function(index) {
	if (this.levels[index]) {
		this.levels[index].dispose();
		this.levels.splice(index, 1);
	}
};

Foreach.prototype.disposeLevels = function() {
	for (var i = 0; i < this.levels.length; i++) {
		this.levels[i].dispose();
	}
	this.levels = [];
};

Foreach.prototype.dispose = function() {
	Core.disposeLinks.call(this);
	this.disposeLevels();
	this.levels = null;
	this.parentElement = null;
	this.parentLevel = null;
	this.items = null;
	this.isRight = null;
	this.getItems = null;
	this.handler = null;
	this.nextSiblingChild = null;
	this.prevSiblingChild = null;
};