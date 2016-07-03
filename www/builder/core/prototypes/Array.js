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