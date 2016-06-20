form OrderCallForm

initial options = {
	'action': 'orderCall/send.php',
	'method': 'POST',
	'container': 'app-order-call',
	'controls': [
		{
			'cmpid': 'name',
			'type': 'text',
			'name': 'name',
			'caption': @contactName,
			'class': 'half-width'
		},
		{
			'cmpid': 'phone',
			'type': 'text',
			'name': 'phone',
			'caption': @contactPhone,
			'class': 'half-width'
		},
		{
			'cmpid': 'email',
			'type': 'text',
			'name': 'email',
			'caption': @contactEmail,
			'class': 'half-width'
		},
		{
			'cmpid': 'topic',
			'type': 'select',
			'name': 'topic',
			'options': Dictionary.get('orderCallTopics'),
			'caption': @callTopic,
			'class': 'half-width'
		}
	],
	'submit': {
		'value': @orderCall,
		'class': 'standart-button green-button send-button'
	}
};

function onRendered() {
	this.setControlValue('name', User.getAttribute('name'));
	this.setControlValue('phone', User.getAttribute('phone'));

	var email = User.getAttribute('email');
	if (email) {
		this.setControlValue('email', email);
		this.enableControl('email', false);
	}
}