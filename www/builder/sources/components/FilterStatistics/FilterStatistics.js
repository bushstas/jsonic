component FilterStatistics

initial controllers = [
	{
		'controller': Filters,
		'on': {
			'load': this.onLoadFilters,
			'count': this.updateFilterCount
		}
	},
	{
		'controller': FiltersStat,
		'on': {
			'load': this.updateFilterCount,
		}
	}
]

function onRendered() {
	FiltersStat.load();
}

function onLoadFilters(data) {
	this.set('filters', data);
}

function updateFilterCount(filterId, counts) {
	this.fill(this.findElement('. ->> row' + filterId), counts);
}