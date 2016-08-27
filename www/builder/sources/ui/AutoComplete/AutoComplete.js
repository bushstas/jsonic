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
			'inputSelector': 'input'
		}
	}
];

function onRendered() {
	var url = this.options{ 'url' };
	if (isString(url)) {

	}
}

function onInput(value) {
	var minLength = this.options{ 'minLength', 3};
}

function onEnter(value) {
	this.clear();
}

function clear() {
	<:input>.clear();
}