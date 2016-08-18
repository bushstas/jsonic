component AutoComplete

initial helpers = [
	{
		'helper': InputHandler,
		'options': {
			'callbacks': {
				'enter': this.onEnter,
				'esc': this.onEscape
			},
			'inputSelector': 'input'
		}
	}
];

function onEnter() {
	
}

function onEscape() {
	
}