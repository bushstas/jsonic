form AuthForm

initial loader = {
	controller: Filters,
	async: true,
	options: {a: this.ass}
}

initial options = {
	'action': 'api/user/login.php',
	'method': 'POST',
	'ajax': true,
	'container': 'app-authform-inputs',
	'controls': [
		{
			'cmpid': 'login',
			'type': 'text',
			'name': 'login',
			'placeholder': 'Введите логин',
			'caption': 'Логин'
		},
		{
			'cmpid': 'password',
			'type': 'password',
			'name': 'password',
			'placeholder': 'Введите пароль',
			'caption': 'Пароль'
		}
	],
	'submit': {
		'value': 'Войти',
		'class': 'app-submit'
	}
};

function onSuccess() {
	window.location.reload();
}