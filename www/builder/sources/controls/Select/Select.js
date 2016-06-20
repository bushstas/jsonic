control Select

function onRendered() {
	var value = this.get('value');
	var selected;
	if (!isUndefined(value)) {
		selected = this.selectByValue(value);
	}
	if (!selected) {
		this.selectByIndex(0);
	}
}

function selectByValue(value) {	
	var options = this.get('options');
	if (isArray(options)) {
		for (var i = 0; i < options.length; i++) {
			if (options[i]['value'] == value) {
				this.selectedIndex = i;
				this.set({
					'value': value,
					'title': options[i]['title']
				});
				return true;
			}
		}
	}
	return false;
}

function selectByIndex(index) {
	this.selectedIndex = index;
	var options = this.get('options');
	if (isObject(options[index])) {
		this.set({
			'value': options[index]['value'],
			'title': options[index]['title']
		});
	}
}

function enableOption(index, isEnabled) {
	var optionsContainer = this.findElement('.app-select-options');
	var optionElement = optionsContainer.getChildAt(index);
	optionElement.toggleClass('disabled', !isEnabled);
	if (index == this.selectedIndex) {
		this.selectByIndex(index == 0 ? index + 1 : 0);
    }
}

function onOptionsClick(e) {
	var target = e.getTarget('.app-select-option');
	if (target && !target.hasClass('disabled')) {
		var value = target.getData('value');
		this.selectByValue(value);
		this.dispatchEvent('change', {'value': value});
		this.hide();
	}
}

function getControlValue() {
	return this.findElement('input').value;
}

function onClick() {
	this.set('active', true);
	Popuper.watch(this, this.findElement('.app-select'));
}

function hide() {
	this.set('active', false);
};