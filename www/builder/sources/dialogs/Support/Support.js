dialog Support;

initial props = {
	'title': @supportDialogTitle
};

function onOrderCallButtonClick() {
	this.hide();
	++> OrderCall
}