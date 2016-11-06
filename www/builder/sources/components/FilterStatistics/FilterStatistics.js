component FilterStatistics

initial loader = {
	'controller': Filters,
	'async': false
}

initial controllers = [
	{
		'controller': FiltersStat,
		'on': {
			'load': this.updateFilterCount
		},
		'private': true
	}
]

initial helpers = [
	{
		'helper': ClickHandler,
		'options': {
			'->> @refresh': this.onRefreshButtonClick,
			'->> @name': this.onFilterClick
		}
	}
]

function onRendered() {
	this.refresh();
}

function onRefreshButtonClick() {
	var a = <.@>;
	$filters.each(function(filter) {
		StoreKeeper.remove('filterStat_' + filter['filterId']);
	});
	this.refresh();
}

function onFilterClick() {
	alert('filter')
}

function refresh() {
	<:rb>.hide();
	this.currentFilterIndex = 0;
	this.getCountForFilterWithIndex(0);	
}

function onLoaded(filters) {
	$filters=>
}

function updateFilterCount(data) {
	this.fill('.->> row' + data['filterId'], data['numbers']);
	this.currentFilterIndex++;
	this.getCountForFilterWithIndex(this.currentFilterIndex);
}

function getCountForFilterWithIndex(index) {
	var filter = $filters{ index };
	if (isObject(filter)) {
		FiltersStat.load({'filterId': filter['filterId']});
	} else {
		<:rb>.show();
	}
}