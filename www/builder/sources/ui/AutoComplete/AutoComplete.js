component AutoComplete

initial helpers = [
	{
		'helper': InputHandler,
		'options': {
			'callbacks': {
				'enter': this.onEnter,
				'esc': this.onEscape,
				'focus': this.onFocus,
				'blur': this.onBlur,
				'input': this.onInput
			},
			'inputSelector': 'input'
		}
	},
	{
		'helper': ClickHandler,
		'options': {
			'->> app-autocomplete-variant': this.onVariantPick
		}
	}
];

initial followers = {
	'variants': this.onChangeVariants
}

function onRendered() {
	var url = this.options{ 'url' };
	if (isString(url)) {
		this.request = new AjaxRequest(url, this.onLoad, null, this);
	}
}

function onInput(value) {
	var len = value.length;
	var minLength = this.options{ 'minLength', 3};
	if (isObject(this.request) && len >= minLength) {
		this.delay(this.load, 1000, value);
	} else if (len == 0) {
		this.delay();
		$variants = [];
	}
}

function load(value) {
	this.request.execute({'token': value});
}

function onLoad(data) {
	$variants = data['items'];
}

function onFocus() {
	if ($variantsCount > 0) {
		$active = true;
	}
}

function onChangeVariants(variants) {
	var count = isArray(variants) ? variants.length : 0;
	$variantsCount = count,
	$active = count > 0;
}

function onBlur() {
	this.delay(function() {
		$active = false;
	}, 200);
}

function onEnter(value) {
	this.clear();
}

function clear() {
	<:input>.clear();
}

function onVariantPick(target) {
	--> pick (target.getData('value'))
}