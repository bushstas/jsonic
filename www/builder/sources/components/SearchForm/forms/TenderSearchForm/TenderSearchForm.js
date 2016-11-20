component TenderSearchForm extends SearchForm

initial args = {
	'title': @searchFormTitle
}

initial listeners = {
	'local': {
		'TenderSearchFormChanged': this.onChange,
		'TenderSearchFormGotParams': this.setParams
	}
}

function onRendered() {
	this.setParams({
		'registryContracts': 1
	});

	delay(6000) {
		$::aaa = 'super-puper-class'
	}
}

function onChange() {
	var data = this.getProperData();
}

function setParams(params:SearchForm) {
	this.setControlsData(params);
}