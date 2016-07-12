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
		}
	}
]

initial helpers = [
	{
		helper: ClickHandler,
		options: {
			'->> app-filter-stat-refresh': this.onRefreshButtonClick,
			'->> app-filter-stat-row-name': this.onFilterClick
		}
	}
]

function onRendered() {	
	this.refresh();
}

function onRefreshButtonClick() {
	this.each('filters', function(filter) {
		StoreKeeper.remove('filterStat_' + filter['filterId']);
	});
	this.refresh();
}

function onFilterClick() {
	alert('filter')
}

function refresh() {
	this.showElement('.->> app-filter-stat-refresh', false);
	this.currentFilterIndex = 0;
	this.getCountForFilterWithIndex(0);	
}

function onLoaded(filters) {
	this.set('filters', filters);
}

function updateFilterCount(data) {
	this.fill(this.findElement('.->>row' + data['filterId']), data['numbers']);
	this.currentFilterIndex++;
	this.getCountForFilterWithIndex(this.currentFilterIndex);
}

function getCountForFilterWithIndex(index) {	
	var filter = Objects.get(this.get('filters'), index);
	if (isObject(filter)) {
		FiltersStat.load({'filterId': filter['filterId']});
	} else {
		this.showElement('.->> app-filter-stat-refresh', true);
	}
}