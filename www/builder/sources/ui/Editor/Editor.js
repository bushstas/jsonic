component Editor

initial followers =  {
	'text': this.onChangeText
}

function edit(element) {
	this.editedElement = element;
	$text = element.innerHTML,
	$shown = true;
	this.placeTo(document.body);
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
	$shown = false;
	this.placeBack();
	--> hide
}