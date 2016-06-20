function Menu() {};

Menu.prototype.onRenderComplete = function() {
	if (Router.hasMenu(this)) {
		this.onNavigate(Router.getCurrentRouteName());
	}
};

Menu.prototype.onNavigate = function(viewName) {
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

Menu.prototype.getButton = function(viewName) {
	return this.findElement('a[role="' + viewName + '"]');
};

Menu.prototype.setButtonActive = function(button, isActive) {
	var activeClassName = this.activeButtonClass || 'active';
	button.toggleClass(activeClassName, isActive);
	if (isActive) {
		this.activeButton = button;
	}
};

Menu.prototype.disposeInternal = function() {
	this.activeButton = null;
};