Array.prototype.contains = function(v) {
	var iv = ~~v;
	if (iv == v) return this.indexOf(iv) > -1 || this.indexOf(v + '') > -1;
	return this.indexOf(v) > -1;
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
	return this.length > 0;
};
Array.prototype.removeItems = function(items) {
	for (var i = 0; i < items.length; i++) this.removeItem(items[i]);
};
Array.prototype.removeItem = function(item) {
	var index = this.indexOf(item);
	if (index > -1) this.splice(index, 1);
};