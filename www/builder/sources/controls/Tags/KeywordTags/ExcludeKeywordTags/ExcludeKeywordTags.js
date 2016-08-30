control ExcludeKeywordTags extends KeywordTags

function onRendered() {
	this.resetOptions();
}

function getCorrectedText(text) {
	var opt1 = $opt1value;
	if (opt1 > 1) {
		return text + '#' + opt1;
	}
	return text;
}

function resetOptions() {
	$opt1 = @noOrder,
	$opt1value = 1;
}