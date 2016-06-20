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