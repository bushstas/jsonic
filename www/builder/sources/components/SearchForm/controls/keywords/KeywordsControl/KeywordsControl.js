control KeywordsControl

initial helpers = [
	{
		'helper': ClickHandler,
		'options': {
			'->> app-keywords-remove-request': this.onRemoveRequestClick
		}
	}
]

function onFocus(isSwitched) {
	$switched = isSwitched;
}


function onRemoveRequestClick(target) {
	var block = target.getAncestor('.->> app-keywords-block');
	var blocks = <.app-keywords-block[]>;
	//this.removeRequest(blocks.indexOf(block), true);
}

function onRecommendationsChange(count) {
	$hasRecomm = count > 0;
}