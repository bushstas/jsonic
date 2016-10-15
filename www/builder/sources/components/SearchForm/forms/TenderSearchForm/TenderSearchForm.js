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

function onResetButtonClick() {
	$reset = true;
	delay(2500) {
		$reset = false;
	}
}

function onResetConfirmed() {

}

function onChange() {
	var data = this.getProperData();
}

function setParams(params:SearchForm) {
	this.setControlsData(params);
}