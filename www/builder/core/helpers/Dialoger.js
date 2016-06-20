function Dialoger() {
	var dialogs = {};
	var currentDialogId, currentDialogClass, currentDialog,
		currentDialogOptions;
	this.show = function(dialogClass, options, dialogId) {
		if (isFunction(dialogClass)) {
			currentDialogClass = dialogClass;
			currentDialogOptions = options;
			defineDialogId(dialogId);
			defineDialog();
			showDialog();
		}
	};
	var defineDialogId = function(dialogId) {
		currentDialogId = currentDialogClass.name + (isString(dialogId) ? '_' + dialogId : '');
	};
	var defineDialog = function() {
		if (isUndefined(dialogs[currentDialogId])) {
			dialogs[currentDialogId] = new currentDialogClass();
			dialogs[currentDialogId].render(document.body);
		}
		currentDialog = dialogs[currentDialogId];
	};
	var showDialog = function() {
		if (isObject(currentDialogOptions)) {
			currentDialog.set(currentDialogOptions);
		}
		currentDialog.show();
	};
	var closeAll = function() {
		for (var k in dialogs) {
			dialogs[k].hide();
		}
	};
	window.addEventListener('popstate', closeAll);
}
Dialoger = new Dialoger();