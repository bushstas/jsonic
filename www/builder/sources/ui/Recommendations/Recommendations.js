component Recommendations

initial controllers = [
	{
		'controller': RecommendationsLoader
	}
]

initial followers = {
	'items': this.onChangeItems
}

function load(words) {
	RecommendationsLoader.load({'excepcions': words});
}

function onChangeItems(items) {
	var itemsCount = items.length;
	$itemsCount = itemsCount;
	--> change (itemsCount)
}