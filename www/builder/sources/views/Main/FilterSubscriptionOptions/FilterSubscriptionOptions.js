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
	this.set({
		'opt1': options['protocolOfFavorite'],
		'opt2': options['protocolOfFilter'],
		'opt3': options['tenderOfFavorite']
	});
}

function onCheckboxChange(e) {
	var params = {};
	params[e['name']] = e['intChecked'];
	Subscription.doAction('save', params);
}

