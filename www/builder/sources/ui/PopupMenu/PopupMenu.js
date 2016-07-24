component PopupMenu

function onRendered() {
	var element = this.getElement();
	this.button = element.parentNode;
	this.addListener(this.button, 'click', this.onShowButtonClick);
}

function onClick(e) {
	var target = e.getTarget('.->> app-popup-menu-button');
	if (!isNull(target)) {
		var idx = target.getData('index');
		var value = target.getData('value');
		var buttons = $buttons;
		if (isArray(buttons) && isObject(buttons[idx]) && isFunction(buttons[idx]['handler'])) {
			buttons[idx]['handler'].call(this, e);
			return;
		}
		this.handleClick(value, target);
	}
}

function onShowButtonClick() {
	this.onBeforeShow();
	this.show();
}

function handleClick(value, button) {}
function onBeforeShow() {}

function show() {
	var outerElement = this.getElement();
	var innerElement = this.findElement('.->> app-popup-menu-inner-container');
	var rect = innerElement.getRect();
	var height = Math.min(rect.height, Objects.get(this.options, 'maxHeight', 400));
	outerElement.setStyle({'max-height': height + 'px', 'height': height + 'px'});
    this.button.addClass('active');
    Popuper.watch(this, outerElement);
}

function hide() {
	var outerElement = this.getElement();
	outerElement.setStyle({'max-height': '0', 'height': '0'});
	this.button.removeClass('active');
}

function renderButtons(items) {
	var buttons = [];
	for (var i = 0; i < items.length; i++) {	
		buttons.push(this.getButtonData(items[i]));
	}
	$buttons = buttons;
}

function getButtonData(item) {
	return {
		'value': item['value'],
		'name': item['name']
	};
};