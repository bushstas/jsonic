control Tags

initial props = {
	'items': []
}

initial helpers = [
	{
		'helper': ClickHandler,
		'options': {
			'->> app-tags-remove': this.onRemoveButtonClick
		}
	}
]

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

function onRemoveButtonClick(e, target) {
	var t = target.getParent().getData('text');
	$items.remove(t);
	this.dispatchChange();
}

function getControlValue() {
	return $items.join(',');
}