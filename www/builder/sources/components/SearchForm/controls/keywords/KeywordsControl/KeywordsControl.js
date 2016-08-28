control KeywordsControl

initial helpers = [
	{
		'helper': ClickHandler,
		'options': {
			'->> app-keywords-add-request': this.addRequest,
			'->> app-keywords-remove-request': this.removeRequest
		}
	}
]

initial followers = {
	'keywords': this.onKeywordsChange
}

function onChange() {
	==> TenderSearchFormChanged
}

function setControlValue(value) {
	var maxlen = value['containKeyword']{ 'length', 0 };
	maxlen = Math.max(maxlen, value['notcontainKeyword']{ 'length', 0 });

	if (maxlen > 0) {
		var kw = [], ck, nck;
		for (var i = 0; i < maxlen; i++) {
			ck = value['containKeyword']{ i, ''}.toArray();
			nck = value['notcontainKeyword']{ i, ''}.toArray();
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

function addRequest() {
	$keywords.addOne([], 0);
};

function removeRequest(target) {
	var index = target.getData('index');
};

function onKeywordsChange(kw) {
	var kwlen = kw.length, tabs = [], i;
	for (i = 1; i <= kwlen; i++) {
		tabs.push(@request + ' ' + i);
	}	
	$keywordsCount = kwlen,
	$tabs = tabs,
	$activeTab = kwlen - 1;
	this.appendChild('tabs', kwlen > 1);
	var markers = <.app-keywords-index[]>;
	for (i = 0; i < markers.length; i++) {
		this.fill(markers[i], {'index': kwlen - i});
	}
}

function onSelectTab(index) {
	index = $keywordsCount - index - 1;
	<:area>.scrollToElement(<.app-keywords-block[index]>, 300);
}

function onRemoveTab(index) {
	$keywords.removeAt(index);
}

function onTagEdit(tag) {
	<::editor>.edit(tag);
	Popuper.skipAll(true);
}

function onTagEdited() {
	Popuper.skipAll(false);
}