form SupportForm extends OrderCallForm

initial args = {
	'action': CONFIG.support.send,
	'controls': [
		#nameInput,
		#emailInput,
		#phoneInput,
		{
			'type': 'textarea',
			'name': 'comment',
			'caption': @descrProblem
		},
		{
			'type': 'file',
			'name': 'screenshot',
			'accept': 'image/*',
			'caption': @attachScreenshot
		}
	],
	'submit': {
		'value': @send,
		'class': @greenButton + ' ->> send-button'
	}
};