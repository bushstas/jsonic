control Keywords

initial helpers = [
	{
		'helper': ClickHandler,
		'options': {
			'->> app-keywords-add-request': this.addRequest,
			'->> app-keywords-remove-request': this.onRemoveRequestClick
		}
	}
]

initial followers = {
	'keywords': this.onKeywordsChange
}

function setControlValue(value) {
	$keywords = value['tags'];
}

function onChange() {
	==> TenderSearchFormChanged
}

function addRequest() {
	$keywords.addOne([], 0);
}

function removeRequest(index, isExact) {
	var total = $keywordsCount;
	$keywords.removeAt(isExact ? index : total - index - 1);
}

function onKeywordsChange(kw) {
	var kwlen = kw.length, tabs = [], i;
	for (i = 1; i <= kwlen; i++) {
		tabs.push(@request + ' ' + i);
	}	
	$keywordsCount = kwlen,
	$tabs = tabs,
	$activeTab = kwlen - 1;
	this.appendChild('tabs', kwlen > 1);	
	this.forChildren(KeywordsControl, function(child, i) {
		child.set('index', kwlen - i);
	});
}

function onSelectTab(index) {
	index = $keywordsCount - index - 1;
	<:area>.scrollToElement(<.app-keywords-block[index]>, 300);
}

function onTagEdit(tag) {
	<::editor>.edit(tag);
	Popuper.skipAll(true);
}

function onTagEdited() {
	Popuper.skipAll(false);
}

function onRemoveRequestClick(target) {
	var block = target.getAncestor('.->> app-keywords-block');
	var blocks = <.app-keywords-block[]>;
	this.removeRequest(blocks.indexOf(block), true);
}