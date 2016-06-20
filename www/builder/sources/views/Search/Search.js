view Search

initial globals = {

};

initial props = {
	'expanded': true
};

function onRendered() {
	this.openInformer();
}


function openInformer() {
	var datatable = this.getChildById('datatable');
}

function openFilter(filterId) {

}

function onFormExpand() {
	this.toggle('expanded');
}