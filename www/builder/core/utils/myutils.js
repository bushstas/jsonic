function getFzName(type) {
	if (type > 4400) return '44 ��';
	if (type < 128) return '94 ��';
	if (type == 256) return '223 ��';
	if (type == 128) return '����';
	return '?? ??';
}