component PopupMenu

function onRendered() {
	this.button = <>.parentNode;
	this.addListener(this.button, 'click', this.onShowButtonClick);
}

function onClick(e) {
	var target = e.getTarget('.->> app-popup-menu-button');
	if (!isNull(target)) {
		get buttons;
		var idx = target.getData('index');
		var value = target.getData('value');
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

function show() {
	var innerElement = <.app-popup-menu-inner-container>;
	var rect = innerElement.getRect();
	var height = Math.min(rect.height, this.options{ 'maxHeight', 400 });
	this.setStyle({maxHeight: height + 'px', height: height + 'px'});
    this.button.addClass('active');
    Popuper.watch(this);
}

function hide() {
	this.setStyle({maxHeight: '0', height: '0'});
	this.button.removeClass('active');
}

function renderButtons(items) {
	var *$buttons = [];
	each (items as item) {	
		buttons.push(this.getButtonData(item));
	}
}

function getButtonData(item) {
	return {
		'value': item['value'],
		'name': item['name']
	};
};

function handleClick() {}
function onBeforeShow() {}