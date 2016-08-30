control Tags

initial props = {
	'items': [],
	'count': 0
}

initial helpers = [
	{
		'helper': ClickHandler,
		'options': {
			'->> app-tags-remove': this.onRemoveButtonClick,
			'->> app-tags-item-text': this.onTagClick
		}
	}
]

initial followers = {
	'items': this.onItemsChange
}

function onEnter(value) {
	var v = value.split(','), a = [], tv;
	for (var i = 0; i < v.length; i++) {
		tv = v[i].trim().toLowerCase();
		if (!tv.isEmpty() && !this.tagExists(tv)) {
			a.push(this.getCorrectedText(tv));
		}
	}
	if (!a.isEmpty()) {
		$items.add(a, 0);
		this.dispatchChange();
	}
}

function tagExists(text) {
	return $items.has(text);
}

function getCorrectedText(text) {
	return text;
}

function onPickVariant(value) {
	this.onEnter(value);
}

function onRemoveButtonClick(target) {
	var text = target.getPrev().getData('text');
	$items.remove(text);
	this.dispatchChange();
}

function onTagClick(target) {
	--> edit (target)
}

function getControlValue() {
	return $items.join(',');
}

function clearControl() {
	$items = [];
}

function onItemsChange(items) {
	$count = items.length;
}

