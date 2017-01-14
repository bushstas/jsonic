component SearchFormCreateFilterMenu extends PopupMenu

initial props = {
	'className': '->> create-filters-menu',
	'buttons': [
		{
			'name': @createNew,
			'handler': this.onCreateButtonClick
		},
		{
			'name': @createWithWizard,
			'handler': this.onWizardButtonClick
		}
	]
};

function onCreateButtonClick() {
	alert('create filter')
}

function onWizardButtonClick() {
	alert('create filter with wizard')	
}