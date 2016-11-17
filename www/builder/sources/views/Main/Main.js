view Main

initial helpers = [
	{
		'helper': ResizeHandler,
		'options': {
			'callback': this.onResize
		}
	}
]

function onRendered() {
	this.onResize();
}

function onResize() {
	var element = <.mainpage-content>;
	element.setHeight('');
	var height = element.getHeight();
	var bodyHeight = document.body.getHeight();
	if (bodyHeight - 100 - height > 0) {
		element.setHeight(bodyHeight - 100);
	}
}