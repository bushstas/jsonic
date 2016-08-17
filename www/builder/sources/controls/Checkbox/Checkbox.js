control Checkbox

function onClick() {
	this.toggle('checked');
	this.dispatchChange();
}

function getControlValue() {
	return $checked ? 1 : 0;
}