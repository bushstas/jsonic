component TenderSearchForm extends SearchForm

initial props = {
	'title': @searchFormTitle
}

initial listeners = {
	'TenderSearchFormChanged': this.onChange,
	'TenderSearchFormGotParams': this.setParams
}

function onRendered() {
	this.setParams({
		'registryContracts': 1
	});

	delay(6000) {
		$:aaa = 'super-puper-class'
		State.dispatchEvent('aaa');
	}
}

function onChange() {
	var data = this.getProperData();
}

function setParams(params:SearchForm) {
	this.setControlsData(params);
}