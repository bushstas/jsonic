control KeywordTags extends Tags

initial helpers = [
	{
		'helper': ClickHandler,
		'options': {
			'->> app-tags-remove-all': this.clear,
			'->> app-tags-select-button': this.onOptionClick
		}
	}
]

function onEnter(value) {
	super(Tags, value);
	this.resetOptions();
}

function onOptionClick(target) {
	var select = this.getChild('opt' + target->index);
	if (select) {
		select.show();
	}
}

function onChangeOption(e, target) {
	var cmpid = target.getId();
	this.set(cmpid, e.title);
	this.set(cmpid + 'value', e.value);
	target.hide();
}

function hasOption(text) {
	return !!text.split('#')[1];
}

function getProperTagText(text) {
	return text.split('#')[0];
}

function tagExists(text) {
	var items = $items.join(',').replace(/\#\d/g, '');
	return items.split(',').has(text);
}

function resetOptions() {};
