control Select

function onRendered() {
	get value;
	var selected;
	if (!isUndefined(value)) {
		selected = this.selectByValue(value, true);
	}
	if (!selected) {
		this.selectByIndex(0);
	}
}

function getChangeEventParams() {
	return {value: $value, title: $title};
}

function selectByValue(value, forced) {
	if (!forced && $value == value) return;
	get options;
	if (isArray(options)) {
		for (var i = 0; i < options.length; i++) {
			if (options[i]['value'] == value) {
				this.selectedIndex = i;
				if (!forced) $value = value;
				$title = options[i]['title'];
				this.syncTooltip(i);
				return true;
			}
		}
	}
	return false;
}

function selectByIndex(index) {
	get options;
	this.selectedIndex = index;
	if (isObject(options[index])) {
		if ($value == options[index]['value']) return;
		$value = options[index]['value'],
		$title = options[index]['title'];
		this.syncTooltip(index);
	}
}

function syncTooltip(index) {
	var optionElement = this.getOptionElementAt(index);
	var tooltipElement = optionElement<.tooltip>;	
}

function enableOption(index, isEnabled) {
	this.getOptionElementAt(index).toggleClass('->> disabled', !isEnabled);
	if (index == this.selectedIndex) {
		this.selectByIndex(index == 0 ? index + 1 : 0);
    }
}

function onOptionsClick(e) {
	var target = e.getTarget('.->> @option');
	if (target && !target.hasClass('->> disabled')) {
		var value = target->value;
		if (this.selectByValue(value)) {
			this.dispatchChange();
		}
		this.hide();
	}
}

function getOptionElementAt(index) {
	return <.@options>.getChildAt(index);
}

function setProperValue(value) {
	this.selectByValue(value);
}

function getControlValue() {
	return <input>.value;
}

function onClick() {
	$active!;
	Popuper.watch(this);
}

function hide() {
	$active = false;
}