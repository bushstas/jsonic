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
		$:aaa = [9,8,7,6,5,4,3,2,1]
		State.dispatchEvent('aaa');
		delay(3000) {
			$:aaa = [100,200,300,400]
			delay(3000) {
				$:aaa = null
			}
		}
	}
}

function onChange() {
	var data = this.getProperData();
}

function setParams(params:SearchForm) {
	this.setControlsData(params);
}