dialog OrderCall;

initial props = {
	'title': @orderCallManager
};

function onSupportButtonClick() {
	this.hide();
	Dialoger.show(Support);
}

function onShow() {
	var form = this.getChildAt(0);
	var handler = form.validateTime.bind(form);
	this.interval = setInterval(handler, 60000);
	handler();
}

function onHide() {
	clearInterval(this.interval);
}