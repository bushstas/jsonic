component SearchFormPanel

function show() {
	this.addClass('->> shown');
	Popuper.watch(this);
}

function hide() {
	this.addClass('->> shown', false);
}