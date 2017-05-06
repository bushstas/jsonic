form SupportForm extends OrderCallForm

initial props = {
	'action': CONFIG.support.send,
	'className': '->> app-order-call',
	'controls': [
		#nameInput,
		#emailInput,
		#phoneInput,
		{
			'caption': @descrProblem,
			'controlClass': Textarea,
			'controlProps': {
				'name': 'comment'
			}
		},
		{
			'caption': @attachScreenshot,
			'controlClass': Input,
			'controlProps': {
				'name': 'screenshot',
				'type': 'file',				
				'accept': 'image/*'
			}			
		}
	],
	'submit': {
		'value': @send,
		'class': @greenButton + ' ->> send-button'
	}
};