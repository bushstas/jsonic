component SearchFormFilters

initial controllers = [
	{
		'controller': Filters,
		'on': {
			'load': this.onLoadFilters
		}
	}
];

initial props = {
	'filterName': 'Master'
};

function onLoadFilters(filters) {
	$quantity = filters.length;
}

function onSaveFilterClick() {
	FilterEdit.show({'filterId': State.get('filterId')});
}