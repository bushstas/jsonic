component Tabs

initial events = {
	'click': {
		'->> @item': this.onSelect,
		'->> @remove': this.onRemove
	}
}

function onSelect(target) {
	var index = target->index;
	$activeTab = index;
	--> select (index)
}

function onRemove(target) {
	var index = target.getParent()->index;
	--> remove (index)
}