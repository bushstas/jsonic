component PopupSelect

function show() {
	$shown = true;
	Popuper.watch(this);
}

function hide() {
	$shown = false;
}