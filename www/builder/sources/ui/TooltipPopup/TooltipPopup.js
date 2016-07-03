component TooltipPopup

initial correctors = {
	'text': this.correctAndSetText
}

function correctAndSetText(text, changedProps) {
	var corrector = changedProps['corrector'];
	if (corrector == 'list') {
		var textParts = text.split('|');
		if (textParts[1]) {
			var temp = [];
			for (var i = 0; i < textParts.length; i++) {
				textPart = textParts[i].split('^');
				textPart = textPart[1] || textPart[0];
				if (textPart.charAt(0) == @softSign) textPart = @otherRegions;
				temp.push(textPart);
			}
			text = temp.removeDuplicates();
		}
	}
	return text;
}