component SearchFormFilters

initial controllers = [
	{
		'controller': Filters,
		'on': {
			'load': this.onLoadFilters
		}
	}
];

function onLoadFilters(filters) {
	this.set('quantity', filters.length);
}

function onSaveFilterClick() {
	Dialoger.show(FilterEdit, {'filterId': Globals.get('filterId')});
}