component TabPanel

function initiate() {
	this.margin = 4;
}

function onRendered() {
	this.redraw();
}

function redraw() {
	this.hiddenTabs = [];
	var tabPanelWidth = this.getElement().getWidth();
	var controlWidth = this.getControlsWidth();
	var tabs = this.findElements('.app-tab');
	var totalWidth = 0, buttonWidth;
	for (var i = 0; i < tabs.length; i++) {
		buttonWidth = tabs[i].getWidth();
		if (totalWidth + controlWidth + buttonWidth + this.margin > tabPanelWidth) {
			tabs[i].show(false);
			this.hiddenTabs.push(i);
		} else {
			tabs[i].style.left = totalWidth + 'px';
			totalWidth += buttonWidth + this.margin;
		}
	}
	this.set('count', this.hiddenTabs.length);
}

function getControlsWidth() {
	var width = 0;
	var restButton = this.findElement('.app-tab-rest');
	if (restButton) width += restButton.getWidth() + this.margin;

	var plusButton = this.findElement('.app-tab-plus');
	if (plusButton) width += plusButton.getWidth() + this.margin;
	return width;
}