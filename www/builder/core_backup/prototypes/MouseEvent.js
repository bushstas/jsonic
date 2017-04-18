{{PROTO}}=MouseEvent.prototype;
{{PROTO}}.getTarget = function(selector) {
	return this.target.getAncestor(selector);
};
{{PROTO}}.getTargetData = function(selector, dataAttr) {
	var target = this.getTarget(selector);
	return !!target ? target.getData(dataAttr) : '';
};
{{PROTO}}.targetHasAncestor = function(element) {
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
{{PROTO}}.targetHasClass = function(className) {
	return this.target.hasClass(className) || (!!this.target.parentNode && this.target.parentNode.hasClass(className));
};
{{PROTO}}.getTargetWithClass = function(className, strict) {
	if (this.target.hasClass(className)) return this.target;
	if (!strict || !this.target.className) {
		if (!!this.target.parentNode && this.target.parentNode.hasClass(className)) return this.target.parentNode;
	}
	return null;
};
