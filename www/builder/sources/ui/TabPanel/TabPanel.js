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

function onRendered() {
	this.tabWidth = this.args['tabWidth'] || 200;
	this.tabMargin = this.args['tabMargin'] || 4;
}

function onParentRendered() {
	var tabs = this.args['tabs'];
	if (isArray(tabs)) {
		each (tabs as tab) {
			this.activateTab(idx, !!tab['active']);
		}
	}
	this.redraw();
}

function redraw() {
	this.hiddenTabs = [];
	var tabPanelWidth = <>.getWidth();
	var controlWidth = this.getControlsWidth();
	var tabs = <.app-tab[]>;
	var totalWidth = 0;
	each (tabs as tab) {
		tab.toggleClass('->> first', idx == 0);
		if (totalWidth + controlWidth + this.tabWidth + this.tabMargin > tabPanelWidth) {
			tab.hide();
			this.hiddenTabs[] = idx;
		} else {
			tab.style.left = totalWidth + 'px';
			totalWidth += this.tabWidth + this.tabMargin;
		}
	}
	$count = this.hiddenTabs.length;
}

function getControlsWidth() {
	var width = 0;
	var restButton = <.app-tab-rest>;
	width += @(restButton).getWidth() + this.tabMargin;

	var plusButton = <.app-tab-plus>;
	width += @(plusButton).getWidth() + this.tabMargin;
	return width;
}

function onRestTabClick() {

}

function onTabClick(target) {
	if (isNumeric(this.activeTab)) {
		this.activateTab(this.activeTab, false);
	}	
	this.activateTab(target->index, true);
}

function activateTab(tabIndex, isShown) {
	var contents = <>.next().finds('.->>app-tab-content');
	@(contents[tabIndex]).show(isShown);
	if (isShown) {
		-->select (tabIndex)
		this.activeTab = tabIndex;		
	}
	<.app-content-tab[tabIndex]>.toggleClass('->> active', isShown);
}