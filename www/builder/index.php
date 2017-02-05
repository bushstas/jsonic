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
		<input type="submit" value="Подсказка по" style="padding: 5px 10px;">
		<select class="topic-select" name="topic">
			<option value="config">конфигу приложения</option>
			<option value="user">авторизации пользователя</option>
			<option value="chain">жизненному циклу</option>
			<option value="shortcut">сокращениям</option>
			<option value="initials">initial параметрам</option>
			<option value="tmp">шаблонам</option>
			<option value="tmpcode">коду в шаблонах</option>
			<option value="attr">зарезерв. атрибуты тегов</option>
			<option value="globals">глобальному состоянию</option>
			<option value="events">событиям</option>
			<option value="dialogs">диалоговым окнам</option>
			<option value="helper">классам хелперам</option>
			<option value="corrector">классам корректорам</option>
			<option value="css">css файлам</option>
			<option value="jsobfus">js обфускации</option>
			<option value="cssobfus">css обфускации</option>
		</select>
	</form>
	<form class="searchform" metod="GET" target="iframe" action="search.php">
		<input type="text" name="search"/>
		<input name="submit" type="submit" value="Искать" style="padding: 5px 10px;">
	</form>
	<form class="testform" metod="GET" target="iframe" action="build.php">
		<input type="hidden" name="istest" value="1"/>
		<input name="submit" type="submit" value="Запустить тест" style="padding: 5px 10px;">
	</form>
	<form metod="GET" target="iframe" action="build.php">
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
				<input type="radio" checked value="1" name="split"> 
				Не разбивать на файлы
			</label>
			<label>
				<input type="radio" value="2" name="split"> 
				Отдельно, каждая часть включает ядро
			</label>
			<label>
				<input type="radio" value="3" name="split"> 
				Отдельно компоненты, ядро также отдельно
			</label>
		</div>
	</form>
</body>
</html>