component SearchForm

initial args = {
	'title': @searchFormTitle
};

function toggleExpand() {
	this.dispatchEvent('expand');
};