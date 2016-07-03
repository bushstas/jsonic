component SearchFormFilterMenu extends PopupMenu

initial args = {
	'className': '->> filters-menu',
	'maxHeight': 400
};

initial controllers = [
	{
		'controller': Filters,
		'on': {
			'load': this.onLoadFilters
		}
	}
];

initial helpers = [
	{
		'helper': CheckboxHandler,
		'options': {
			'callback': this.onCheckboxChange,
			'intValue': true,
			'checkboxClass': '->> app-ui-checkbox',
			'checkboxCheckedClass': '->> checked',
			'labelClass': null
		}
	}
];

function onLoadFilters(filters) {
	this.renderButtons(filters);
}

function onCheckboxChange(e) {
	Filters.doAction('set', {'filterId': e.value, 'param': 'isAutoOpen', 'value': e.checked});
}

function getButtonData(item) {
	return {
		'value': item['filterId'],
		'name': item['header'],
		'isAutoOpen': item['isAutoOpen']
	};
};

function handleClick(value, button) {
	Globals.getView('search').openFilter(value);
};