component TenderSearchForm extends FiltersForm

function initiate() {
	Globals.listen('TenderSearchFormChanged', this.onChange, this);
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
	var data = this.getProperData(this.getControlsData());
	console.log(data)
}