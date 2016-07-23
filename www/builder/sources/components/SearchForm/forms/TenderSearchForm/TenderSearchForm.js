component TenderSearchForm

function onResetButtonClick(e) {
	var button = e.target.getAncestor('.->> app-search-form-reset');
	button.addClass('->> active');
	this.delay(function() {
		button.removeClass('->> active');
	}, 2500);
};

function onResetConfirmed() {

};
