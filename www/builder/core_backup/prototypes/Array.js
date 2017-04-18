{{PROTO}}=Array.prototype;
{{PROTO}}.contains = function(v) {
	var iv = ~~v;
	if (iv == v) return this.indexOf(iv) > -1 || this.indexOf(v + '') > -1;
	return this.has(v);
};

{{PROTO}}.has = function(v) {
	return this.indexOf(v) > -1;
};
{{PROTO}}.hasAny = function(a) {
	if (!isArray(a)) a = arguments;
	for (var i = 0; i < a.length; i++) {
		if (this.indexOf(a[i]) > -1) return true;
	}
};
{{PROTO}}.hasExcept = function() {
	var args = Array.prototype.slice.call(arguments);
	for (var i = 0; i < this.length; i++) {
		if (args.indexOf(this[i]) == -1) return true;
	}
};
{{PROTO}}.removeDuplicates = function() {
	this.filter(function(item, pos, self) {
	    return self.indexOf(item) == pos;
	});
	return this;
};
{{PROTO}}.getIntersections = function(arr) {
	return this.filter(function(n) {
	    return arr.indexOf(n) != -1;
	});
};
{{PROTO}}.hasIntersections = function(arr) {
	return !isUndefined(this.getIntersections(arr)[0]);
};
{{PROTO}}.removeIndexes = function(indexes) {
	var deleted = 0;
	for (var i = 0; i < indexes.length; i++) {
		this.splice(indexes[i] - deleted, 1);
		deleted++;
	}
};
{{PROTO}}.isEmpty = function() {
	return this.length == 0;
};
{{PROTO}}.removeItems = function(items) {
	for (var i = 0; i < items.length; i++) this.removeItem(items[i]);
};
{{PROTO}}.removeItem = function(item) {
	var index = this.indexOf(item);
	if (index > -1) this.splice(index, 1);
};
{{PROTO}}.insertAt = function(item, index) {
	if (!isNumber(index) || index >= this.length) this.push(item);
	else this.splice(index, 0, item);
};
{{PROTO}}.shuffle = function() {
	var tmp;
	for (var i = this.length - 1; i > 0; i--) {
		var j = Math.floor(Math.random() * (i + 1));
		tmp = this[i];
		this[i] = this[j];
		this[j] = tmp;
	}
};
{{PROTO}}.addUnique = function(item) {
	if (!this.has(item)) this.push(item);
};
{{PROTO}}.addRemove = function(item, add, addUnique) {
	if (add) {
		if (addUnique) this.addUnique(item);
		else this.push(item);
	} else this.removeItem(item);
};