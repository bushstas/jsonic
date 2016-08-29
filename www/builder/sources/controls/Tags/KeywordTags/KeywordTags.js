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

function onOptionClick(target) {
	var index = target.getData('index');
	var select = this.getChild('opt' + index);
	if (select) {
		select.show();
	}
}