form AuthForm

initial options = {
	'action': 'user/login.php',
	'method': 'POST',
	'ajax': true,
	'container': 'app-authform-inputs',
	'controls': [
		{
			'cmpid': 'login',
			'type': 'text',
			'name': 'login',
			'placeholder': @enterLogin,
			'caption': @login
		},
		{
			'cmpid': 'password',
			'type': 'password',
			'name': 'password',
			'placeholder': @enterPassword,
			'caption': @password
		}
	],
	'submit': {
		'value': @enter,
		'class': 'app-submit'
	}
};

function onSuccess() {
	window.location.reload();
}