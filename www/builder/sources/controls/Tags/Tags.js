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
		tv = v[i].trim();
		if (!tv.isEmpty() && !$items.has(tv)) a.push(tv);
	}
	if (!a.isEmpty()) {
		$items.add(a, 0);
		this.dispatchChange();
	}
}

function onPickVariant(value) {
	this.onEnter(value);
}

function onRemoveButtonClick(target) {
	var t = target.getParent().getData('text');
	$items.remove(t);
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

