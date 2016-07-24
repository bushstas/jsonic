component FilterSubscriptionOptions

initial loader = {
	'controller': Subscription
}

initial helpers = [
	{
		'helper': CheckboxHandler,
		'options': {
			'callback': this.onCheckboxChange,			
			'labelClass': '->> app-subscription-option'
		}
	}
];

function onLoaded(data) {
	var options = data['options'];
	$opt1 = options['tenderOfFavorite'],
	$opt2 = options['protocolOfFavorite'],
	$opt3 = options['protocolOfFilter']
}

function onCheckboxChange(e) {
	var params = {};
	params[e['name']] = e['intChecked'];
	Subscription.doAction('save', params);
}

