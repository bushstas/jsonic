control KeywordsControl

function onChange() {
	Globals.dispatchEvent('TenderSearchFormChanged');
}

function setControlValue(value) {
	$keywords = [[value['containKeyword'], value['notcontainKeyword']]];
}