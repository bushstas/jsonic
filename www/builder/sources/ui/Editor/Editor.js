component Editor

initial followers =  {
	'text': this.onChangeText
}

function edit(element) {
	this.editedElement = element;
	$text = element.innerHTML,
	$shown = true;
	this.reposition();
}

function reposition(text) {
	this.placeTo(document.body);
	var rect = this.editedElement.getRect();
	this.setPosition(rect.left, rect.top);
}

function onChangeText(text) {
	var input = <input>;
	input.value = text;
	input.focus();
}

function onEnter(value) {
	this.editedElement.innerHTML = value;
	this.hide();
}

function hide() {
	this.close();
	this.placeBack();
	--> hide
}

function close() {
	$shown = false;
}