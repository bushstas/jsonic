function getFzName(type) {
	if (type > 4400) return '44 ิว';
	if (type < 128) return '94 ิว';
	if (type == 256) return '223 ิว';
	if (type == 128) return 'สฮฬฬ';
	return '?? ??';
}