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

function onSelect(target) {
	var index = target.getData('index');
	$activeTab = index;
	--> select (index)
}

function onRemove(target) {
	var tab = target.getParent();
	var index = tab.getData('index');
	--> remove (index)
}