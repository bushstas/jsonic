_c = function() {};
_p=_c.prototype;
_p.doRendering = function() {
	{{GLOBAL}}.get('Component').prototype.doRendering.call(this);
	var router = {{GLOBAL}}.get('Router');
	if (router.hasMenu(this)) {
		this.onNavigate(router.getCurrentRouteName());
	}
};
_p.onNavigate = function(viewName) {
	if (this.rendered) {
		if (isElement(this.activeButton)) {
			this.setButtonActive(this.activeButton, false);	
		}
		var button = this.getButton(viewName);
		if (isElement(button)) {
			this.setButtonActive(button, true);
		}
	}
};
_p.getButton = function(viewName) {
	return this.findElement('a[role="' + viewName + '"]');
};
_p.setButtonActive = function(button, isActive) {
	var activeClassName = this.activeButtonClass || '->> active';
	button.toggleClass(activeClassName, isActive);
	if (isActive) {
		this.activeButton = button;
	}
};
_p.disposeInternal = function() {
	this.activeButton = null;
};
{{GLOBAL}}.set(_c, 'Menu');