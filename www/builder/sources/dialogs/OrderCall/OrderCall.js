dialog OrderCall;

initial props = {
	'title': @orderCallManager
};

function onSupportButtonClick() {
	this.hide();
	Dialoger.show(Support);
}