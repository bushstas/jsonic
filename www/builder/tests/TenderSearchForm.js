test before setParams {
	if (!isObject(params)) {
		log('params is not an object');
		params = {};
	}
}