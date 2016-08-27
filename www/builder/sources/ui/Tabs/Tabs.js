component Tabs

initial helpers = [
	{
		'helper': ClickHandler,
		'options': {
			'->> app-tabs-item': this.onSelect,
			'->> app-tabs-remove': this.onRemove
		}
	}
]

function onSelect(e, target) {
	var index = target.getData('index');
	$activeTab = index;
	--> select (index)
}

function onRemove(e, target) {
	var tab = target.getParent();
	var index = tab.getData('index');
	--> remove (index)
}