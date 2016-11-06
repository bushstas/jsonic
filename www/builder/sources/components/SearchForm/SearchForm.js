component SearchForm

function onResetButtonClick() {
	$reset = true;
	delay(2500) {
		$reset = false;
	}
}

function onResetConfirmed() {

}

function getProperData(data) {
	return Objects.flatten(this.getControlsData());
}