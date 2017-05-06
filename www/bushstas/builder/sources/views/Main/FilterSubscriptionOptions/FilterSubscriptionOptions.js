component FilterSubscriptionOptions

initial loader = {
	'controller': Subscription
}

initial helpers = [
	{
		'helper': CheckboxHandler,
		'options': {
			'callback': this.onCheckboxChange,			
			'labelClass': '->> @option'
		}
	}
]

function onCheckboxChange(e) {
	var params = {};
	params[e['name']] = e['intChecked'];
	Subscription.save(params);
}

