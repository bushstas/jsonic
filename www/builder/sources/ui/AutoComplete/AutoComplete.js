component AutoComplete

initial helpers = [
	{
		'helper': InputHandler,
		'options': {
			'callbacks': {
				'enter': this.onEnter,
				'esc': this.onEscape,
				'focus': this.onFocus,
				'blur': this.onBlur,
				'input': this.onInput
			},
			'inputSelector': 'input[type="text"]'
		}
	}
];

function onRendered() {
	this.input = this.findElement('input[type="text"]');
	this.container = this.findElement('.->> app-autocomplete-variants');
	var url = Objects.get(this.options, 'url');
	if (isString(url)) {

	}
}

function onFocus() {
	this.dispatchEvent('focus');
}

function onBlur() { 
	this.dispatchEvent('blur');
}

function onInput(value) {
	var minLength = Objects.get(this.options, 'minLength', 3);

}

function onEnter(value) {
	this.dispatchEvent('enter', value);
}

function onEscape() {
	this.dispatchEvent('escape');
}