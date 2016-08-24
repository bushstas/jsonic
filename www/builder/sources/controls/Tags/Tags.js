control Tags

initial props = {
	'items': []
}

function onEnter(value) {
	var v = value.split(',');
	for (var i = 0; i < v.length; i++) {
		if (!$items.has(v[i])) $items.add(v[i], 0);
	}
}

