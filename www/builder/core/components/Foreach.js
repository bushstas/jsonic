function Foreach(params) {
	this.getItems = params['p'];
	this.items = params['p']();
	this.handler = params['h'];
	this.isRight = !!params['r'];
	this.isRandom = !!params['ra'];
	this.ifEmpty = params['ie'];
	this.limit = ~~params['l'];
	this.levels = [];

	var getKeysInRandomOrder = function() {
		var keys = Objects.getKeys(this.items);
		keys.shuffle();
		return keys;
	};

	var createIfEmptyLevel = function() {
		if (!isUndefined(this.ifEmpty)) {
			this.createLevel(this.ifEmpty);
		}
	};

	this.createLevels = function(isUpdating) {
		if (isArrayLike(this.items)) {
			if (this.isRandom) {
				if (!Objects.empty(this.items)) {
					var keys = getKeysInRandomOrder.call(this);
					for (var i = 0; i < keys.length; i++) {
						if (this.limit && i + 1 > this.limit) break;
						this.createLevel(this.handler(this.items[keys[i]], keys[i]), isUpdating);
					}
					return;
				}
			} else if (isArray(this.items)) {
				if (!this.items.isEmpty()) {
					if (!this.isRight) {
						for (var i = 0; i < this.items.length; i++) {
							if (this.limit && i + 1 > this.limit) break;
							this.createLevel(this.handler(this.items[i], i), isUpdating);
						}
					} else {
						var j = 0;
						for (var i = this.items.length - 1; i >= 0; i--) {
							j++;
							if (this.limit && j > this.limit) break;
							this.createLevel(this.handler(this.items[i], i), isUpdating);
						}
					}
					return;
				}
			} else if (isObject(this.items)) {
				if (!Objects.empty(this.items)) {
					if (!this.isRight) {
						var i = 0;
						for (var k in this.items) {
							i++;
							if (this.limit && i > this.limit) break;
							this.createLevel(this.handler(this.items[k], k), isUpdating);
						}
					} else {
						var keys = Objects.getKeys(this.items);
						keys.reverse();
						for (var i = 0; i < keys.length; i++) {
							if (this.limit && i + 1 > this.limit) break;
							this.createLevel(this.handler(this.items[keys[i]], keys[i]), isUpdating);
						}
					}
					return;
				}
			}
		}
		createIfEmptyLevel.call(this)
	};

	this.update = function(items) {
		this.items = this.getItems();
		this.disposeLevels();
		this.createLevels(true);
	};

	this.add = function(item, index) {
		this.createLevel(this.handler(item, ~~index), false, index);	
	};

	this.remove = function(index) {
		if (this.levels[index]) {
			this.levels[index].dispose();
			this.levels.splice(index, 1);
		}
	};

	this.dispose = function() {
		Core.disposeLinks.call(this);
		this.disposeLevels();
		this.levels = null;
		this.parentElement = null;
		this.parentLevel = null;
		this.items = null;
		this.isRight = null;
		this.ifEmpty = null;
		this.getItems = null;
		this.handler = null;
		this.nextSiblingChild = null;
		this.prevSiblingChild = null;
	};
}

Foreach.prototype.render = function(parentElement, parentLevel) {
	this.parentElement = parentElement;
	this.parentLevel = parentLevel;
	this.createLevels(false);
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

Foreach.prototype.disposeLevels = function() {
	for (var i = 0; i < this.levels.length; i++) {
		this.levels[i].dispose();
	}
	this.levels = [];
};