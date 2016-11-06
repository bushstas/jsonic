component TenderSearchForm extends SearchForm

initial args = {
	'title': @searchFormTitle
};

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

function onChange() {
	var data = this.getProperData();
}

function setParams(params:SearchForm) {
	this.setControlsData(params);
}