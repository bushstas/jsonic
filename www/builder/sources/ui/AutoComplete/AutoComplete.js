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
				'input': this.onInput,
				'up': this.onUp,
				'down': this.onDown
			},
			'inputSelector': 'input'
		}
	},
	{
		'helper': ClickHandler,
		'options': {
			'->> @variant': this.onVariantPick
		}
	}
];

initial followers = {
	'variants': this.onChangeVariants,
	'currentVariant': this.onChangeCurrentVariant,
	'active': this.onChangeActive
}

function onInput(value) {
	var len = value.length;
	var minLength = this.options{ 'minLength', 3};
	if (isString(this.options{ 'url' }) && len >= minLength) {
		this.delay(this.load, 1000, value);
	} else if (len == 0) {
		this.delay();
		$variants = [];
	}
}

function load(value) {
	Loader.get(this.options{ 'url' }, {'token': value}, this.onLoad, this);
}

function onLoad(data) {
	$currentVariant = null;
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
	get currentVariant;
	if (isNumber(currentVariant)) {
		var e = <.@variant.active>;
		--> enter (e->value)
		this.onEscape();
		return false;
	} else {
		this.clear();
	}
}

function setValue(value) {
	<input>.value = value;
}

function onEscape() {
	this.clear();
}

function clear() {
	this.delay();
	<:input>.clear();
	$variants = [];
}

function onVariantPick(target) {
	--> pick (target.getData('value'))
	this.clear();
}

function onUp() {
	this.highlightVariant(-1);
}

function onDown() {
	this.highlightVariant(1);
}

function highlightVariant(step) {
	get variants, currentVariant;
	if (isArray(variants) && variants.length > 0) {
		var total = variants.length;
		if (!isNumber(currentVariant)) {
			currentVariant = -1;
		}
		currentVariant += step;
		if (currentVariant < 0) {
			currentVariant = total - 1;
		} else if (currentVariant == total) {
			currentVariant = 0;
		}
		$currentVariant=>
	}
}

function onChangeCurrentVariant(index) {
	var e = <.@variant.active>;
	@(e).removeClass('->> active');
	e = <.@variant[index]>;
	@(e).addClass('->> active');
}

function onChangeActive(isActive) {
	if (!isActive) $currentVariant = null;
}