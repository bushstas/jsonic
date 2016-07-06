MouseEvent.prototype.getTarget = function(selector) {
	return this.target.getAncestor(selector);
};
MouseEvent.prototype.targetHasAncestor = function(element) {
	if (isElement(element)) {
		var target = this.target;		
		while (target) {
			if (target == element) {
				return true;
			}
			target = target.parentNode;
		}
	}
	return false;
};
MouseEvent.prototype.targetHasClass = function(className) {
	return this.target.hasClass(className) || (!!this.target.parentNode && this.target.parentNode.hasClass(className));
};
MouseEvent.prototype.getTargetWithClass = function(className) {
	if (this.target.hasClass(className)) return this.target;
	if (!!this.target.parentNode && this.target.parentNode.hasClass(className)) return this.target.parentNode;
	return null;
};
