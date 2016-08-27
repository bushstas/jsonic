component Dialog

initial args = {
	'closable': true
}

initial followers = {
	'width': this.reposition,
	'height': this.reposition
}

initial props = {
	'width': 600
}

function show() {
	$shown = true;
	this.reposition();	
	this.onShow();
}

function reposition() {
	var rect = <>.getRect();	
	$marginTop = Math.round(rect.height / -2) + 'px',
	$marginLeft = Math.round(rect.width / -2) + 'px';	
}

function hide() {
	$shown = false;
}

function close() {
	this.hide();
	this.onHide();
}

function expand() {
	$expanded!;
}

function onShow() {}
function onHide() {}