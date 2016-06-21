form SupportForm extends OrderCallForm

initial options = {
	'action': CONFIG.support.send,
	'controls': [
		#nameInput,
		#emailInput,
		#phoneInput,
		{
			'cmpid': 'comment',
			'type': 'textarea',
			'name': 'comment',
			'caption': @descrProblem
		},
		{
			'cmpid': 'screenshot',
			'type': 'file',
			'name': 'screenshot',
			'accept': 'image/*',
			'caption': @attachScreenshot
		}
	],
	'submit': {
		'value': @send,
		'class': @greenButton + ' send-button'
	}
};