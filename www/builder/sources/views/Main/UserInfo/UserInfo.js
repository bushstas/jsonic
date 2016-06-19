component UserInfo

initial loader = {
	'controller': UserInfoLoader,
	'async': true 
}

function onLoaded(data) {
	if (!User.hasFullAccess()) {
		data['prolongButtonText'] = @orderTariff;
	} else if (data['needToProlong']) {
		data['prolongButtonText'] = @prolongAccess;
	}
	this.set(data);
}

function onOrderCallButtonClick() {
	Dialoger.show(OrderCall);	
}