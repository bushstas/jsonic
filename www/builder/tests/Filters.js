
test after onLoadFilters {
	assertObject(data, 'error text');
	assertArrayLike(data['data']['items'][0], 'error text');
}

test before onSubscribe {
	if (!isNumber(index, 2)) {
		log('error text');
	}
}