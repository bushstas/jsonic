_c = function(params) {
	var handler = params['h'];
	var isRight = !!params['r'];
	var isRandom = !!params['ra'];
	var ifEmpty = params['ie'];
	this.levels = [];

	var getKeysInRandomOrder = function() {
		var keys = Objects.getKeys(getItems());
		keys.shuffle();
		return keys;
	};

	var createIfEmptyLevel = function() {
		if (!isUndefined(ifEmpty)) {
			this.createLevel(ifEmpty);
		}
	};

	var getItems = function() {
		if (isFunction(params['p'])) {
			return params['p']();
		} 
		return params['p'];
	};

	var getLimit = function() {
		if (isFunction(params['l'])) {
			return params['l']();
		} 
		return ~~params['l'];
	};

	this.createLevels = function(isUpdating) {
		var items = getItems();
		var limit = getLimit();
		if (isArrayLike(items)) {
			if (isRandom) {
				if (!Objects.empty(items)) {
					var keys = getKeysInRandomOrder();
					for (var i = 0; i < keys.length; i++) {
						if (limit && i + 1 > limit) break;
						this.createLevel(handler(items[keys[i]], keys[i]), isUpdating);
					}
					return;
				}
			} else if (isArray(items)) {
				if (!items.isEmpty()) {
					if (!isRight) {
						for (var i = 0; i < items.length; i++) {
							if (limit && i + 1 > limit) break;
							this.createLevel(handler(items[i], i), isUpdating);
						}
					} else {
						var j = 0;
						for (var i = items.length - 1; i >= 0; i--) {
							j++;
							if (limit && j > limit) break;
							this.createLevel(handler(items[i], i), isUpdating);
						}
					}
					return;
				}
			} else if (isObject(items)) {
				if (!Objects.empty(items)) {
					if (!isRight) {
						var i = 0;
						for (var k in items) {
							i++;
							if (limit && i > limit) break;
							this.createLevel(handler(items[k], k), isUpdating);
						}
					} else {
						var keys = Objects.getKeys(items);
						keys.reverse();
						for (var i = 0; i < keys.length; i++) {
							if (limit && i + 1 > limit) break;
							this.createLevel(handler(items[keys[i]], keys[i]), isUpdating);
						}
					}
					return;
				}
			}
		}
		createIfEmptyLevel.call(this)
	};

	this.update = function() {
		this.disposeLevels();
		this.createLevels(true);
	};

	this.add = function(item, index) {
		this.createLevel(handler(item, ~~index), false, index);	
	};

	this.remove = function(index) {
		if (this.levels[index]) {
			this.levels[index].dispose();
			this.levels.splice(index, 1);
		}
	};

	this.dispose = function() {
		{{GLOBAL}}.get('Core').disposeLinks.call(this);
		this.disposeLevels();
		this.levels = null;
		this.parentElement = null;
		this.parentLevel = null;
		this.nextSiblingChild = null;
		this.prevSiblingChild = null;
		handler = null;
		params = null;
	};
}
_p=_c.prototype;
_p.render = function(parentElement, parentLevel) {
	this.parentElement = parentElement;
	this.parentLevel = parentLevel;
	this.createLevels(false);
};
_p.createLevel = function(items, isUpdating, index) {
	var level = new {{GLOBAL}}.get('Level')(this.parentLevel.getComponent());
	var nextSiblingChild;
	if (isNumber(index) && this.levels[index]) {
		nextSiblingChild = this.levels[index].getFirstNodeChild();
	} else {
		nextSiblingChild = isUpdating ? {{GLOBAL}}.get('Core').getNextSiblingChild.call(this) : null;
	}
	level.render(items, this.parentElement, this.parentLevel, nextSiblingChild);
	this.levels.insertAt(level, index);
};
_p.disposeLevels = function() {
	for (var i = 0; i < this.levels.length; i++) {
		this.levels[i].dispose();
	}
	this.levels = [];
};
{{GLOBAL}}.set(_c, 'Foreach');