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
]

function onCheckboxChange(e) {
	var params = {};
	params[e['name']] = e['intChecked'];
	Subscription.doAction('save', params);
}

