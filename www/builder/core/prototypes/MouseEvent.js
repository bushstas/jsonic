_p=MouseEvent.prototype;
_p.getTarget = function(selector) {
	return this.target.getAncestor(selector);
};
_p.getTargetData = function(selector, dataAttr) {
	var target = this.getTarget(selector);
	return !!target ? target.getData(dataAttr) : '';
};
_p.targetHasAncestor = function(element) {
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
_p.targetHasClass = function(className) {
	return this.target.hasClass(className) || (!!this.target.parentNode && this.target.parentNode.hasClass(className));
};
_p.getTargetWithClass = function(className, strict) {
	if (this.target.hasClass(className)) return this.target;
	if (!strict || !this.target.className) {
		if (!!this.target.parentNode && this.target.parentNode.hasClass(className)) return this.target.parentNode;
	}
	return null;
};
