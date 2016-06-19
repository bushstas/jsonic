component SearchFormCreateFilterMenu extends PopupMenu

initial args = {
	'className': 'create-filters-menu'
};

initial props = {
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