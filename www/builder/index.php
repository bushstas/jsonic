<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
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
</style>
</head>
<body>
	<form metod="GET" target="iframe" action="build.php">
		<div class="top">
			<input type="submit" value="��������������" style="padding: 5px 10px;">
		</div>
		<div style="clear: both;"></div>
		<iframe name="iframe" id="iframe" width="70%" height="1000"></iframe>
		<div class="options">
			<label>
				<input type="checkbox" vlaue="1" name="create"> 
				������� ���������
			</label>
			<label>
				<input type="checkbox" vlaue="1" name="advanced"> 
				��������� ����������
			</label>
		</div>
	</form>
</body>
</html>