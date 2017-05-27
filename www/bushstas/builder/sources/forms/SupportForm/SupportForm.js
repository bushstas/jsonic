form SupportForm extends OrderCallForm

initial props = {
	'action': Api.support.send,
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
		'class': '->> standart-button ->> green-button ->> send-button'
	}
};