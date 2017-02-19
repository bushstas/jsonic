{{COMPONENT}} = function() {};
{{PROTO}}={{COMPONENT}}.prototype;
{{PROTO}}.doRendering = function() {
	{{GLOBAL}}.get('Component').prototype.doRendering.call(this);
	var router = {{GLOBAL}}.get('Router');
	if (router.hasMenu(this)) {
		this.onNavigate(router.getCurrentRouteName());
	}
};
{{PROTO}}.onNavigate = function(viewName) {
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
{{PROTO}}.getButton = function(viewName) {
	return this.findElement('a[role="' + viewName + '"]');
};
{{PROTO}}.setButtonActive = function(button, isActive) {
	var activeClassName = this.activeButtonClass || '->> active';
	button.toggleClass(activeClassName, isActive);
	if (isActive) {
		this.activeButton = button;
	}
};
{{PROTO}}.disposeInternal = function() {
	this.activeButton = null;
};
{{GLOBAL}}.set({{COMPONENT}}, 'Menu');