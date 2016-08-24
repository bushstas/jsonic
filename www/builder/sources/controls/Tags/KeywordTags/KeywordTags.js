control KeywordTags extends Tags

initial helpers = [
	{
		'helper': ClickHandler,
		'options': {
			'->> app-tags-remove-all': this.clear
		}
	}
]