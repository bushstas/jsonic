component KeywordsAutoComplete extends AutoComplete

initial options = {
	'url': CONFIG.keywords.get
}

function onAddButtonClick() {
	this.onEnter(this.input.value);
}