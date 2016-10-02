function Core() {}
Core.prototype._getNextSiblingChild = function() {
	if (!this.nextSiblingChild) return null;
	if (this.nextSiblingChild instanceof Node) return this.nextSiblingChild;
	var firstNodeChild = this.nextSiblingChild._getFirstNodeChild();
	if (firstNodeChild) return firstNodeChild;
	return this.nextSiblingChild._getNextSiblingChild();	
};
Core.prototype._setNextSiblingChild = function(nextSiblingChild) {
	this.nextSiblingChild = nextSiblingChild;
	if (!(nextSiblingChild instanceof Node)) this.nextSiblingChild._setPrevSiblingChild(this);
};
Core.prototype._setPrevSiblingChild = function(prevSiblingChild) {
	this.prevSiblingChild = prevSiblingChild;
};
Core.prototype._disposeLinks = function() {
	if (this.prevSiblingChild) this.prevSiblingChild._setNextSiblingChild(this.nextSiblingChild);
	this.prevSiblingChild = null;
	this.nextSiblingChild = null;
};
Core.prototype._getFirstNodeChild = function() {
	if (this.level) return this.level.getFirstNodeChild();
	return null;
};