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
		padding: 5px 6px;
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
		<select class="topic-select" name="topic">
			<option value="config">конфиг. приложения</option>
			<option value="api">конфигурация API</option>
			<option value="user">авторизация пользователя</option>
			<option value="chain">жизненный цикл</option>
			<option value="shortcut">сокращения</option>
			<option value="initials">initial параметры</option>
			<option value="tmp">шаблоны</option>
			<option value="tmpcode">код в шаблонах</option>
			<option value="attr">зарезерв. атрибуты тегов</option>
			<option value="globals">глобальное состояние</option>
			<option value="events">события</option>
			<option value="dialogs">диалоговые окна</option>
			<option value="helper">классы хелперы</option>
			<option value="corrector">классы корректоры</option>
			<option value="css">css файлы</option>
			<option value="jsobfus">js обфускация</option>
			<option value="cssobfus">css обфускация</option>
		</select>
		<input type="submit" value="?" style="padding: 5px 10px;">
	</form>
	<form class="searchform" metod="GET" target="iframe" action="search.php">
		<input type="text" name="search"/>
		<input name="submit" type="submit" value="Искать" style="padding: 5px 10px;">
	</form>
	<form class="testform" metod="GET" target="iframe" action="build.php">
		<input type="hidden" name="istest" value="1"/>
		<input name="submit" type="submit" value="Запустить тест" style="padding: 5px 10px;">
	</form>
	<form metod="POST" target="iframe" action="build.php">
		<div class="top">
			<input name="submit" type="submit" value="Скомпилировать" style="padding: 5px 10px;">
		</div>
		<div style="clear: both;"></div>
		<iframe name="iframe" id="iframe" width="70%" height="940"></iframe>
		<div class="options">
			<label>
				<input type="checkbox" value="1" name="create"> 
				Создать окружение
			</label>
			<label>
				<input type="checkbox" value="1" name="advanced"> 
				Усиленная обфускация
			</label>
			<label>
				<input type="checkbox" value="1" name="js_obfuscate"> 
				Обфускация js кода
			</label>
			<label>
				<input type="checkbox" value="1" name="obfuscate"> 
				Обфускация css классов
			</label>

			<label>
				<input type="checkbox" value="1" name="split"> 
				Разбивать на js на отдельные файлы
			</label>
		</div>
	</form>
</body>
</html>