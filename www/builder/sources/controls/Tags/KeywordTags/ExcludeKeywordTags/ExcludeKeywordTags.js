control ExcludeKeywordTags extends KeywordTags

function onRendered() {
	this.resetOptions();
}

function getCorrectedText(text) {
	get opt1value;
	if (opt1value > 1) {
		return text + '#' + opt1value;
	}
	return text;
}

function resetOptions() {
	$opt1 = @noOrder,
	$opt1value = 1;
}