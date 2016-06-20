component Dialog

initial args = {
	'closable': true
};

initial followers = {
	'width': this.reposition,
	height: this.reposition
};

initial props = {
	'width': 600
};

function show() {
	this.set('shown', true);
	this.reposition();	
	this.onShow();
};

function reposition() {
	var element = this.getElement();
	var rect = element.getRect();
	this.set({
		'marginTop': Math.round(rect.height / -2) + 'px',
		'marginLeft': Math.round(rect.width / -2) + 'px'
	});
};
function hide() {
	this.set('shown', false);
	this.onHide();
};

function expand() {
	this.toggle('expanded');
};

function onShow() {};
function onHide() {};