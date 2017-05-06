component Dialog

initial props = {
	'closable': true,
	'width': 600
}

initial followers = {
	'width': this.reposition,
	'height': this.reposition
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

function expand(isExpanded) {
	if (isBool(isExpanded)) {
		$expanded = isExpanded;
	} else {
		$expanded!;
	}
}

function onShow() {}
function onHide() {}