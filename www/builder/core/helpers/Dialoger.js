function Dialoger() {
	var dialogs = {};
	var currentDialogId, currentDialogClass, currentDialog,
		currentDialogOptions;
	this.show = function(dialogClass, options, dialogId) {
		if (isFunction(dialogClass)) {
			currentDialogOptions = options;
			defineDialogId(dialogClass, dialogId);
			defineDialog();
			showDialog();
		}
	};
	this.hide = function(dialogClass, dialogId) {
		defineDialogId(dialogClass, dialogId);
		if (dialogs[currentDialogId]) dialogs[currentDialogId].close();
	};
	this.get = function(dialogClass, dialogId) {
		defineDialogId(dialogClass, dialogId);
		return dialogs[currentDialogId];
	};
	var defineDialogId = function(dialogClass, dialogId) {
		currentDialogClass = dialogClass;
		currentDialogId = currentDialogClass.name + (isString(dialogId) ? '_' + dialogId : '');
	};
	var defineDialog = function() {
		if (isUndefined(dialogs[currentDialogId])) {
			dialogs[currentDialogId] = new currentDialogClass();
			Core.initiate.call(dialogs[currentDialogId]);
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