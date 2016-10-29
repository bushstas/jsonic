Array.prototype.contains = function(v) {
	var iv = ~~v;
	if (iv == v) return this.indexOf(iv) > -1 || this.indexOf(v + '') > -1;
	return this.has(v);
};

Array.prototype.has = function(v) {
	return this.indexOf(v) > -1;
};
Array.prototype.hasAny = function(a) {
	if (!isArray(a)) a = arguments;
	for (var i = 0; i < a.length; i++) {
		if (this.indexOf(a[i]) > -1) return true;
	}
};
Array.prototype.hasExcept = function() {
	var args = Array.prototype.slice.call(arguments);
	for (var i = 0; i < this.length; i++) {
		if (args.indexOf(this[i]) == -1) return true;
	}
};
Array.prototype.removeDuplicates = function() {
	this.filter(function(item, pos, self) {
	    return self.indexOf(item) == pos;
	});
	return this;
};
Array.prototype.getIntersections = function(arr) {
	return this.filter(function(n) {
	    return arr.indexOf(n) != -1;
	});
};
Array.prototype.hasIntersections = function(arr) {
	return !isUndefined(this.getIntersections(arr)[0]);
};
Array.prototype.removeIndexes = function(indexes) {
	var deleted = 0;
	for (var i = 0; i < indexes.length; i++) {
		this.splice(indexes[i] - deleted, 1);
		deleted++;
	}
};
Array.prototype.isEmpty = function() {
	return this.length == 0;
};
Array.prototype.removeItems = function(items) {
	for (var i = 0; i < items.length; i++) this.removeItem(items[i]);
};
Array.prototype.removeItem = function(item) {
	var index = this.indexOf(item);
	if (index > -1) this.splice(index, 1);
};
Array.prototype.insertAt = function(item, index) {
	if (!isNumber(index) || index >= this.length) this.push(item);
	else this.splice(index, 0, item);
};