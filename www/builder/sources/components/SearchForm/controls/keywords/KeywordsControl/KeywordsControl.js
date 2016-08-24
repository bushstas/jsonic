control KeywordsControl

function onChange() {
	Globals.dispatchEvent('TenderSearchFormChanged');
}

function setControlValue(value) {
	var maxlen = 0;
	if (isArray(value['containKeyword'])) {
		maxlen = Math.max(maxlen, value['containKeyword'].length);
	}
	if (isArray(value['notcontainKeyword'])) {
		maxlen = Math.max(maxlen, value['notcontainKeyword'].length);
	}
	if (maxlen > 0) {
		var kw = [], ck, nck;
		for (var i = 0; i < maxlen; i++) {
			ck = Objects.get(value['containKeyword'], i, '').toArray();
			nck = Objects.get(value['notcontainKeyword'], i, '').toArray();
			kw.push([ck, nck]);
		}
		$keywords = kw;
	} else {
		$keywords = [[]];
	}
}

function onFocus(isSwitched) {
	$switched = isSwitched;
}
