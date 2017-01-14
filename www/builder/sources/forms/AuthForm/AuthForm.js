form AuthForm

initial props = {
	'action': 'user/login.php',
	'ajax': true,
	'className': '->> app-authform-inputs',
	'controls': [
		{
			'caption': @login,
			'controlClass': Input,
			'controlProps': {
				'type': 'text',
				'name': 'login',
				'placeholder': @enterLogin
			}
		},
		{
			'caption': @password,
			'controlClass': Input,
			'controlProps': {
				'type': 'password',
				'name': 'password',
				'placeholder': @enterPassword
			}			
		}
	],
	'submit': {
		'value': @enter,
		'class': '->> app-submit'
	}
};

function onSuccess() {
	Router.reload();
}