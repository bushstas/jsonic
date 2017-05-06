control ContainKeywordTags extends KeywordTags

function onRendered() {
	this.resetOptions();
}

function onPickRecommendation(value) {
	
}

function onEnter(value) {
	super(KeywordTags, value);
	var items = $items.join(',').replace(/\#\d/g, '').split(',');
	<::recommendations>.load(items);
}


function getCorrectedText(text) {
	var opt1 = $opt1value;
	var opt2 = $opt2value;
	if (opt1 > 1 || opt2 > 1) {
		return text + '#' + opt1 + '#' + opt2;
	}
	return text;
}

function resetOptions() {
	$opt1 = @noOrder,
	$opt2 = @withinParagraph;
	$opt1value = 1;
	$opt2value = 1;
}