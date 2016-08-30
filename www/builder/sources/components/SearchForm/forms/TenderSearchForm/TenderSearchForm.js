component TenderSearchForm extends FiltersForm

function initiate() {
	Globals.addListeners({
		'TenderSearchFormChanged': this.onChange,
		'TenderSearchFormGotParams': this.setParams
	}, this);
}

function onRendered() {
	this.setParams({
		'registryContracts': 1
	});
}

function onResetButtonClick(e) {
	var button = e.target.getAncestor('.->> app-search-form-reset');
	button.addClass('->> active');
	this.delay(function() {
		button.removeClass('->> active');
	}, 2500);
}

function onResetConfirmed() {

}

function onChange() {
	var data = this.getProperData();
}

function setParams(params:SearchForm) {
	this.setControlsData(params);
}