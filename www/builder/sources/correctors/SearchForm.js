corrector SearchForm

function correct(params) {
	var tags = [];
	var maxlen = params['containKeyword']{ 'length', 0 };
	maxlen = Math.max(maxlen, params['notcontainKeyword']{ 'length', 0 });

	if (maxlen > 0) {
		var ck, nck;
		for (var i = 0; i < maxlen; i++) {
			ck = params['containKeyword']{ i, ''}.toArray();
			nck = params['notcontainKeyword']{ i, ''}.toArray();
			tags.push([ck, nck]);
		}
	} else {
		tags.push([]);
	}
	return {
		'keywords': {
			'nonmorph': params['nonmorph'],
			'registryContracts': params['registryContracts'],
			'registryProducts': params['registryProducts'],
			'searchInDocumentation': params['searchInDocumentation'],
			'tags': tags
		}
	};
}