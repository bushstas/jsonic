component TabPanel

initial helpers = [
	{
		'helper': ClickHandler,
		'options': {
			'->> app-tab-rest': this.onRestTabClick,
			'->> app-tab': this.onTabClick
		}
	}
]

function initiate() {
	this.margin = 4;
}

function onRendered() {
	this.doOnParentReady(this.onParentReady);
	this.redraw();
}

function onParentReady() {
	if (isArray(this.args['tabs'])) {
		for (var i = 0; i < this.args['tabs'].length; i++) {
			this.activateTab(i, !!this.args['tabs'][i]['active']);
		}
	}
}

function redraw() {
	this.hiddenTabs = [];
	var tabPanelWidth = this.getElement().getWidth();
	var controlWidth = this.getControlsWidth();
	var tabs = this.findElements('.->> app-tab');
	var totalWidth = 0, buttonWidth;
	for (var i = 0; i < tabs.length; i++) {
		tabs[i].toggleClass('->> first', i == 0);
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
	var restButton = this.findElement('.->> app-tab-rest');
	if (restButton) width += restButton.getWidth() + this.margin;

	var plusButton = this.findElement('.->> app-tab-plus');
	if (plusButton) width += plusButton.getWidth() + this.margin;
	return width;
}

function onRestTabClick() {

}

function onTabClick(e, target) {
	if (isNumeric(this.activeTab)) this.activateTab(this.activeTab, false);
	this.activateTab(target.getData('index'), true);
}

function activateTab(tabIndex, isShown) {
	var container = this.findElement('.' + Objects.get(this.getTabParamsByIndex(tabIndex), 'container'), this.getScopeElement());
	if (container) container.show(isShown);
	if (isShown) this.activeTab = tabIndex;
	this.getTab(tabIndex).toggleClass('->> active', isShown);
}

function getTab(tabIndex) {
	return this.findElements('.->> app-content-tab')[tabIndex];
}

function getScopeElement() {
	if (!this.scopeElement && isString(this.args['scope'])) this.scopeElement = this.findElementWithinParent('.' + this.args['scope']) || document.body;
	return this.scopeElement;
}

function getTabParamsByIndex(tabIndex) {
	return this.args['tabs'][tabIndex];
};