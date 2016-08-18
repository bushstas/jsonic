component TenderSearchForm extends FiltersForm

function initiate() {
	Globals.addListeners({
		'TenderSearchFormChanged': this.onChange,
		'TenderSearchFormGotParams': this.onGetParams
	}, this);
}

function onRendered() {
	this.onGetParams({
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
	console.log(this.getControlsData())
}

function onGetParams(params) {
	var data = {
		'keywords': {
			'nonmorph': params['nonmorph'],
			'registryContracts': params['registryContracts'],
			'registryProducts': params['registryProducts'],
			'searchInDocumentation': params['searchInDocumentation'],
			'containKeyword': params['containKeyword'],
			'notcontainKeyword': params['notcontainKeyword']
		}
	};
	this.setControlsData(data);
}