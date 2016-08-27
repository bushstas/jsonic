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
	var element = <.app-mainpage-content>;
	element.setHeight('');
	var height = element.getHeight();
	var bodyHeight = document.body.getHeight();
	var diff = bodyHeight - 100 - height;
	if (diff > 0) {
		element.setHeight(bodyHeight - 100);
	}
}