component TabPanel

initial events = {
	'click': {
		'->> tab-rest': this.onRestTabClick,
		'->> content-tab': this.onTabClick
	}
}

function onRendered() {
	get tabs;
	this.tabWidth = $tabWidth || 200;
	this.tabMargin = $tabMargin || 4;

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
	var tabs = <.@tab[]>;
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
	var restButton = <.tab-rest>;
	width += @(restButton).getWidth() + this.tabMargin;

	var plusButton = <.tab-plus>;
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

	var contents = <>.finds('.->>' + ($containerClass || 'tab-content'));
	console.log(contents)
	@(contents[tabIndex]).show(isShown);
	if (isShown) {
		-->select (tabIndex)
		this.activeTab = tabIndex;		
	}
	<.content-tab[tabIndex]>.toggleClass('->> active', isShown);
}