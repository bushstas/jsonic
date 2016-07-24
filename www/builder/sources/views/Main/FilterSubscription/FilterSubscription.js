component FilterSubscription

initial loader = {
	'controller': Filters
}

initial helpers = [
	{
		'helper': ClickHandler,
		'options': {
			'->> subscr-button': this.onSubscribeButtonClick
		}
	}
];


function onLoaded(filters) {
	$filters = filters;
	$total = this.getTotalCount(),
	$subscribed = this.getSubscribedCount();	
}

function getTotalCount() {
	return Decliner.getCount('filter', $filters);
}

function getSubscribedCount() {
	var subscribedCount = 0;
	this.each('filters', function(filter) {
		if (filter['isSubs'] == 1) subscribedCount++;
	});
	return Decliner.getCount('subscr', subscribedCount);
}

function onFreqChange(e) {

}

function onSubscribeButtonClick(e, target) {
	var filterId = e.getTargetData('.->> app-subscription-filter-row', 'filterId');
	if (filterId) {
		Filters.doAction('subscribe', {
			'filterId': filterId,
			'value': target.hasClass('->> subscribed') ? '0' : '1'
		});
	}
}