<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=windows-1251">
<style>
	body {
		font-family: Segoe UI, Georgia, Arial;
	}
	input {
		font-family: Segoe UI, Georgia, Arial;	
	}
	label {
		display: block;
		cursor: pointer;
		padding: 5px 0;
		line-height: 16px;
	}
	select {
		font-family: Segoe UI, Georgia, Arial;
		padding: 4px 6px;
	}
	input[type="checkbox"] {
		float: left;
		margin-right: 10px;
		margin-top: 3px;
	}
	.top {
		margin-bottom: 10px;
	}
	.options {
		position: absolute;
		right: 0;
		width: 30%;
		padding-left: 16px;
		top: 50px;
		box-sizing: border-box;
	}
	.hint-form {
		position: absolute;
		top: 10px;
		right: 30%;
	}
	.topic-select {
		width: 200px;
	}
	.testform {
		position: absolute;
		left: 145px;
	}
	.searchform {
		position: absolute;
		left: 265px;
	}
	.searchform input[type="text"] {
		padding: 4px 4px 5px 4px;
		width: 150px;
	}
</style>
</head>
<body>
	<form class="hint-form" method="GET" target="iframe" action="builder-php-classes/hint.php">
		<input type="submit" value="��������� ��" style="padding: 5px 10px;">
		<select class="topic-select" name="topic">
			<option value="config">������� ����������</option>
			<option value="user">����������� ������������</option>
			<option value="chain">���������� �����</option>
			<option value="shortcut">�����������</option>
			<option value="initials">initial ����������</option>
			<option value="tmp">��������</option>
			<option value="tmpcode">���� � ��������</option>
			<option value="attr">��������. �������� �����</option>
			<option value="globals">����������� ���������</option>
			<option value="events">��������</option>
			<option value="dialogs">���������� �����</option>
			<option value="helper">������� ��������</option>
			<option value="corrector">������� �����������</option>
			<option value="css">css ������</option>
			<option value="jsobfus">js ����������</option>
			<option value="cssobfus">css ����������</option>
		</select>
	</form>
	<form class="searchform" metod="GET" target="iframe" action="search.php">
		<input type="text" name="search"/>
		<input name="submit" type="submit" value="������" style="padding: 5px 10px;">
	</form>
	<form class="testform" metod="GET" target="iframe" action="build.php">
		<input type="hidden" name="istest" value="1"/>
		<input name="submit" type="submit" value="��������� ����" style="padding: 5px 10px;">
	</form>
	<form metod="GET" target="iframe" action="build.php">
		<div class="top">
			<input name="submit" type="submit" value="��������������" style="padding: 5px 10px;">
		</div>
		<div style="clear: both;"></div>
		<iframe name="iframe" id="iframe" width="70%" height="940"></iframe>
		<div class="options">
			<label>
				<input type="checkbox" value="1" name="create"> 
				������� ���������
			</label>
			<label>
				<input type="checkbox" value="1" name="advanced"> 
				��������� ����������
			</label>
			<label>
				<input type="checkbox" value="1" name="js_obfuscate"> 
				���������� js ����
			</label>
			<label>
				<input type="checkbox" value="1" name="obfuscate"> 
				���������� css �������
			</label>

			<label>
				<input type="radio" checked value="1" name="split"> 
				�� ��������� �� �����
			</label>
			<label>
				<input type="radio" value="2" name="split"> 
				��������, ������ ����� �������� ����
			</label>
			<label>
				<input type="radio" value="3" name="split"> 
				�������� ����������, ���� ����� ��������
			</label>
		</div>
	</form>
</body>
</html>