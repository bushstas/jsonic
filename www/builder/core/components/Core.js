function Core() {}
Core.prototype.getNextSiblingChild = function() {
	if (!this.nextSiblingChild) {
		return null;
	}
	if (this.nextSiblingChild instanceof Node) {
		return this.nextSiblingChild;
	}
	var firstNodeChild = this.nextSiblingChild.getFirstNodeChild();
	if (firstNodeChild) {
		return firstNodeChild;
	}
	return this.nextSiblingChild.getNextSiblingChild();	
};
Core.prototype.setNextSiblingChild = function(nextSiblingChild) {
	this.nextSiblingChild = nextSiblingChild;
	if (!(nextSiblingChild instanceof Node)) {
		this.nextSiblingChild.setPrevSiblingChild(this);
	}
};
Core.prototype.setPrevSiblingChild = function(prevSiblingChild) {
	this.prevSiblingChild = prevSiblingChild;
};
Core.prototype.disposeLinks = function() {
	if (this.prevSiblingChild) {
		this.prevSiblingChild.setNextSiblingChild(this.nextSiblingChild);
	}
	this.prevSiblingChild = null;
	this.nextSiblingChild = null;
};
Core.prototype.setScope = function(scope) {
	this.parentLevel.setScope(scope);
};
Core.prototype.getPropName = function(i) {
	this.parentLevel.getPropName(i);
};