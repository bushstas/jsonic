control Select

function onRendered() {
	var value = $value;
	var selected;
	if (!isUndefined(value)) {
		selected = this.selectByValue(value, true);
	}
	if (!selected) {
		this.selectByIndex(0);
	}
}

function selectByValue(value, forced) {
	if (!forced && $value == value) return;
	var options = $options;
	if (isArray(options)) {
		for (var i = 0; i < options.length; i++) {
			if (options[i]['value'] == value) {
				this.selectedIndex = i;
				if (!forced) $value = value;
				$title = options[i]['title'];
				return true;
			}
		}
	}
	return false;
}

function selectByIndex(index) {
	this.selectedIndex = index;
	var options = $options;
	if (isObject(options[index])) {
		if ($value == options[index]['value']) return;
		$value = options[index]['value'],
		$title = options[index]['title'];
	}
}

function enableOption(index, isEnabled) {
	var optionsContainer = this.findElement('.->> app-select-options');
	var optionElement = optionsContainer.getChildAt(index);
	optionElement.toggleClass('->> disabled', !isEnabled);
	if (index == this.selectedIndex) {
		this.selectByIndex(index == 0 ? index + 1 : 0);
    }
}

function onOptionsClick(e) {
	var target = e.getTarget('.->> app-select-option');
	if (target && !target.hasClass('->> disabled')) {
		var value = target.getData('value');
		if (this.selectByValue(value)) this.dispatchChange();
		this.hide();
	}
}

function setProperValue(value) {
	this.selectByValue(value);
}

function getControlValue() {
	return this.findElement('input').value;
}

function onClick() {
	this.toggle('active');
	Popuper.watch(this, this.findElement('.->> app-select'));
}

function hide() {
	$active = false;
};