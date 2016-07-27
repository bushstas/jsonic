form AuthForm

initial args = {
	'action': 'user/login.php',
	'method': 'POST',
	'ajax': true,
	'container': '->> app-authform-inputs',
	'controls': [
		{
			'type': 'text',
			'name': 'login',
			'placeholder': @enterLogin,
			'caption': @login
		},
		{
			'type': 'password',
			'name': 'password',
			'placeholder': @enterPassword,
			'caption': @password
		}
	],
	'submit': {
		'value': @enter,
		'class': '->> app-submit'
	}
};

function onSuccess() {
	window.location.reload();
}