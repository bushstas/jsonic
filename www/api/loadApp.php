<?php 

$opts = array('http' => array('header'=> 'Cookie: ' . $_SERVER['HTTP_COOKIE']."\r\n"));
$context = stream_context_create($opts);
$user = file_get_contents('http://jsonic.ru/api/user/login.php', false, $context);

die('{"user": '.$user.', "dictionary":'.file_get_contents('http://jsonic.ru/api/dictionary/get.php?page='.$_GET['page']).', "texts":["121212","++++++++++++++++","\u0413\u043b\u0430\u0432\u043d\u0430\u044f","\u041f\u043e\u0438\u0441\u043a","\u0418\u0437\u0431\u0440\u0430\u043d\u043d\u043e\u0435","\u041f\u043b\u0430\u043d\u044b \u0437\u0430\u043a\u0443\u043f\u043e\u043a","\u0410\u043d\u0430\u043b\u0438\u0442\u0438\u043a\u0430","\u0417\u0430\u043f\u0440\u0430\u0448\u0438\u0432\u0430\u0435\u043c\u043e\u0439 \u0441\u0442\u0440\u0430\u043d\u0438\u0446\u044b \u043d\u0435 \u0441\u0443\u0449\u0435\u0441\u0442\u0432\u0443\u0435\u0442","\\u00A0","Delete","11111111"], "textsConstants":["\u0420\u0435\u0433\u0438\u043e\u043d\u044b","\u041a\u0430\u0442\u0435\u0433\u043e\u0440\u0438\u0438","\u0414\u0440\u0443\u0433\u0438\u0435 \u0441\u0442\u0440\u0430\u043d\u044b \u0438 \u0440\u0435\u0433\u0438\u043e\u043d\u044b","\u044c","\u0420\u0410\u0421\u0428\u0418\u0420\u0415\u041d\u041d\u042b\u0419 \u041f\u041e\u0418\u0421\u041a","\u0421\u043e\u0437\u0434\u0430\u0442\u044c \u043d\u043e\u0432\u044b\u0439","\u0421\u043e\u0437\u0434\u0430\u0442\u044c \u043c\u0430\u0441\u0442\u0435\u0440\u043e\u043c","\u0421\u043e\u0437\u0434\u0430\u0442\u044c \u0444\u0438\u043b\u044c\u0442\u0440","\u041c\u043e\u0438 \u0444\u0438\u043b\u044c\u0442\u0440\u044b","\u0421\u043e\u0445\u0440\u0430\u043d\u0438\u0442\u044c","\u041e\u0447\u0438\u0441\u0442\u0438\u0442\u044c \u0432\u0435\u0441\u044c \u0444\u0438\u043b\u044c\u0442\u0440","\u0423\u0432\u0435\u0440\u0435\u043d\u044b?","\u0414\u0430","\u041f\u043e\u0438\u0441\u043a \u043f\u043e \u043a\u043b\u044e\u0447\u0435\u0432\u044b\u043c \u0441\u043b\u043e\u0432\u0430\u043c","\u0441 \u0443\u0447\u0435\u0442\u043e\u043c \u043c\u043e\u0440\u0444\u043e\u043b\u043e\u0433\u0438\u0438","\u043f\u043e \u0442\u043e\u0447\u043d\u043e\u043c\u0443 \u0441\u043e\u043e\u0442\u0432\u0435\u0442\u0441\u0442\u0432\u0438\u044e","\u0418\u0441\u043a\u0430\u0442\u044c","\u0432","\u0434\u043e\u043a\u0443\u043c\u0435\u043d\u0442\u0430\u0446\u0438\u0438","\u0440\u0435\u0435\u0441\u0442\u0440\u0435 \u043a\u043e\u043d\u0442\u0440\u0430\u043a\u0442\u043e\u0432","\u043f\u0440\u043e\u0434\u0443\u043a\u0442\u0430\u0445","\u0414\u043e\u0431\u0430\u0432\u0438\u0442\u044c \u0437\u0430\u043f\u0440\u043e\u0441","\u0421\u043e\u0434\u0435\u0440\u0436\u0438\u0442","\u0418\u0441\u043a\u043b\u044e\u0447\u0430\u0435\u0442","\u0412\u0432\u0435\u0434\u0438\u0442\u0435 \u043a\u043b\u044e\u0447\u0435\u0432\u044b\u0435 \u0441\u043b\u043e\u0432\u0430 \u0447\u0435\u0440\u0435\u0437 \u0437\u0430\u043f\u044f\u0442\u0443\u044e","\u0414\u043e\u0431\u0430\u0432\u0438\u0442\u044c","\u0414\u043e\u0431\u0430\u0432\u043b\u0435\u043d\u043e \u0441\u043b\u043e\u0432:","\u0423\u0434\u0430\u043b\u0438\u0442\u044c \u0432\u0441\u0435","\u0417\u0430\u043f\u0440\u043e\u0441","\u0443\u0434\u0430\u043b\u0438\u0442\u044c \u0437\u0430\u043f\u0440\u043e\u0441","\u041f\u043e\u0440\u044f\u0434\u043e\u043a \u0432\u0432\u0435\u0434\u0435\u043d\u043d\u044b\u0445 \u0441\u043b\u043e\u0432","\u0420\u0430\u0441\u0441\u0442\u043e\u044f\u043d\u0438\u0435 \u043c\u0435\u0436\u0434\u0443 \u0441\u043b\u043e\u0432\u0430\u043c\u0438","\u0432 \u043b\u044e\u0431\u043e\u043c \u043f\u043e\u0440\u044f\u0434\u043a\u0435","\u0432 \u0443\u043a\u0430\u0437\u0430\u043d\u043d\u043e\u043c \u043f\u043e\u0440\u044f\u0434\u043a\u0435","\u043d\u0435 \u0434\u0430\u043b\u0435\u0435 \u0430\u0431\u0437\u0430\u0446\u0430","0 \u0441\u043b\u043e\u0432 (\u043e\u0431\u044f\u0437\u0430\u0442\u0435\u043b\u044c\u043d\u043e \u0440\u044f\u0434\u043e\u043c)","1 \u0441\u043b\u043e\u0432\u043e \u0438 \u043c\u0435\u043d\u0435\u0435","2 \u0441\u043b\u043e\u0432\u0430 \u0438 \u043c\u0435\u043d\u0435\u0435","3 \u0441\u043b\u043e\u0432\u0430 \u0438 \u043c\u0435\u043d\u0435\u0435","4 \u0441\u043b\u043e\u0432\u0430 \u0438 \u043c\u0435\u043d\u0435\u0435","5 \u0441\u043b\u043e\u0432 \u0438 \u043c\u0435\u043d\u0435\u0435","\u0418\u0441\u043a\u0430\u0442\u044c \u0441\u043b\u043e\u0432\u0430 \u0438\u0441\u043a\u043b\u044e\u0447\u0435\u043d\u0438\u044f","\u0432 \u0440\u0430\u043c\u043a\u0430\u0445 \u043e\u0434\u043d\u043e\u0433\u043e \u044d\u043b\u0435\u043c\u0435\u043d\u0442\u0430","\u0432 \u043b\u044e\u0431\u043e\u043c \u044d\u043b\u0435\u043c\u0435\u043d\u0442\u0435","\u0422\u0435\u043d\u0434\u0435\u0440\u044b \u0432 \u0438\u0437\u0431\u0440\u0430\u043d\u043d\u043e\u043c, \u043a\u043e\u0442\u043e\u0440\u044b\u0435 \u0437\u0430\u0432\u0435\u0440\u0448\u0430\u044e\u0442\u0441\u044f","dd month yyyy","\u0417\u0430\u043a\u0430\u0437 \u0437\u0432\u043e\u043d\u043a\u0430 \u043c\u0435\u043d\u0435\u0434\u0436\u0435\u0440\u0430","\u0421\u043e\u0437\u0434\u0430\u0442\u044c \u0437\u0430\u044f\u0432\u043a\u0443 \u0432 \u0442\u0435\u0445\u043f\u043e\u0434\u0434\u0435\u0440\u0436\u043a\u0443","\u0417\u0430\u044f\u0432\u043a\u0430 \u0432 \u0442\u0435\u0445\u043f\u043e\u0434\u0434\u0435\u0440\u0436\u043a\u0443","\u0417\u0430\u043a\u0430\u0437\u0430\u0442\u044c \u0437\u0432\u043e\u043d\u043e\u043a \u043c\u0435\u043d\u0435\u0434\u0436\u0435\u0440\u0430","->> standart-button","->> standart-button ->> green-button","->> standart-button ->> red-button","->> standart-button ->> white-button","\u0412\u0432\u0435\u0434\u0438\u0442\u0435 \u043b\u043e\u0433\u0438\u043d","\u041b\u043e\u0433\u0438\u043d","\u0412\u0432\u0435\u0434\u0438\u0442\u0435 \u043f\u0430\u0440\u043e\u043b\u044c","\u041f\u0430\u0440\u043e\u043b\u044c","\u0412\u043e\u0439\u0442\u0438","\u041a\u043e\u043d\u0442\u0430\u043a\u0442\u043d\u043e\u0435 \u0438\u043c\u044f","\u041a\u043e\u043d\u0442\u0430\u043a\u0442\u043d\u044b\u0439 \u0442\u0435\u043b\u0435\u0444\u043e\u043d","Email","\u0422\u0435\u043c\u0430 \u0437\u0432\u043e\u043d\u043a\u0430","\u0423\u0434\u043e\u0431\u043d\u044b\u0439 \u0434\u0435\u043d\u044c \u0437\u0432\u043e\u043d\u043a\u0430","\u0423\u0434\u043e\u0431\u043d\u043e\u0435 \u0432\u0440\u0435\u043c\u044f \u0437\u0432\u043e\u043d\u043a\u0430","\u041a\u0440\u0430\u0442\u043a\u043e\u0435 \u043e\u043f\u0438\u0441\u0430\u043d\u0438\u0435","\u0441\u0435\u0433\u043e\u0434\u043d\u044f","\u0437\u0430\u0432\u0442\u0440\u0430","\u041e\u043f\u0438\u0441\u0430\u043d\u0438\u0435 \u043f\u0440\u043e\u0431\u043b\u0435\u043c\u044b","\u041f\u0440\u0438\u043a\u0440\u0435\u043f\u0438\u0442\u044c \u0441\u043a\u0440\u0438\u043d\u0448\u043e\u0442","\u041e\u0442\u043f\u0440\u0430\u0432\u0438\u0442\u044c","\u041f\u043d","\u0412\u0442","\u0421\u0440","\u0427\u0442","\u041f\u0442","\u0421\u0431","\u0412\u0441","\u0421\u043e\u0445\u0440\u0430\u043d\u0435\u043d\u0438\u0435 \u0444\u0438\u043b\u044c\u0442\u0440\u0430","\u0420\u0435\u0434\u0430\u043a\u0442\u0438\u0440\u043e\u0432\u0430\u043d\u0438\u0435","\u0415\u0449\u0435","\u041b\u0418\u0427\u041d\u042b\u0419 \u041a\u0410\u0411\u0418\u041d\u0415\u0422","\u041a\u043b\u0438\u0435\u043d\u0442","\u041e\u0440\u0433\u0430\u043d\u0438\u0437\u0430\u0446\u0438\u044f","Email","\u0422\u0430\u0440\u0438\u0444\u043d\u044b\u0439 \u043f\u043b\u0430\u043d","\u041d\u0430\u0447\u0430\u043b\u043e \u0434\u043e\u0441\u0442\u0443\u043f\u0430","\u041e\u043a\u043e\u043d\u0447\u0430\u043d\u0438\u0435 \u0434\u043e\u0441\u0442\u0443\u043f\u0430","http:\/\/initpro.ru\/tariffs\/","\u0422\u0430\u0440\u0438\u0444\u043d\u044b\u0435 \u043f\u043b\u0430\u043d\u044b","\u0412\u0430\u0448 \u043f\u0435\u0440\u0441\u043e\u043d\u0430\u043b\u044c\u043d\u044b\u0439 \u043c\u0435\u043d\u0435\u0434\u0436\u0435\u0440","http:\/\/initpro.ru\/order\/#1","\u0417\u0430\u043a\u0430\u0437\u0430\u0442\u044c \u0442\u0430\u0440\u0438\u0444","\u041f\u0440\u043e\u0434\u043b\u0438\u0442\u044c \u0434\u043e\u0441\u0442\u0443\u043f","8-800-700-12-50","\u0417\u0432\u043e\u043d\u043e\u043a \u0431\u0435\u0441\u043f\u043b\u0430\u0442\u043d\u044b\u0439","\u0438\u043b\u0438","\u0417\u0430\u043a\u0430\u0437\u0430\u0442\u044c \u0437\u0432\u043e\u043d\u043e\u043a","\u041a\u0430\u043b\u0435\u043d\u0434\u0430\u0440\u044c \u043c\u043e\u0438\u0445 \u0437\u0430\u043a\u0443\u043f\u043e\u043a","\u041e\u0431\u0449\u0430\u044f \u0438\u043d\u0444\u043e\u0440\u043c\u0430\u0446\u0438\u044f","\u041f\u0430\u0440\u0430\u043c\u0435\u0442\u0440\u044b \u0440\u0430\u0441\u0441\u044b\u043b\u043a\u0438","\u041d\u0430\u0441\u0442\u0440\u043e\u0439\u043a\u0438","\u0421\u0442\u0430\u0442\u0438\u0441\u0442\u0438\u043a\u0430 \u043f\u043e \u043c\u043e\u0438\u043c \u0444\u0438\u043b\u044c\u0442\u0440\u0430\u043c","\u041e\u0431\u043d\u043e\u0432\u0438\u0442\u044c","\u041d\u0430\u0437\u0432\u0430\u043d\u0438\u0435","\u0417\u0430 \u0441\u0435\u0433\u043e\u0434\u043d\u044f","\u0417\u0430 \u0432\u0447\u0435\u0440\u0430","\u0422\u0435\u043a\u0443\u0449\u0438\u0445","\u0417\u0430 \u043d\u0435\u0434\u0435\u043b\u044e","\u0417\u0430 \u043c\u0435\u0441\u044f\u0446","\u0423\u0432\u0435\u0434\u043e\u043c\u043b\u044f\u0442\u044c \u043c\u0435\u043d\u044f \u043e \u043f\u043e\u044f\u0432\u043b\u0435\u043d\u0438\u0438","\u0438\u0437\u043c\u0435\u043d\u0435\u043d\u0438\u0439 \u043f\u043e \u0442\u0435\u043d\u0434\u0435\u0440\u0430\u043c \u0432 \u0418\u0437\u0431\u0440\u0430\u043d\u043d\u043e\u043c","\u043f\u0440\u043e\u0442\u043e\u043a\u043e\u043b\u043e\u0432 \u043f\u043e \u0442\u0435\u043d\u0434\u0435\u0440\u0430\u043c \u0432 \u0418\u0437\u0431\u0440\u0430\u043d\u043d\u043e\u043c","\u043d\u043e\u0432\u044b\u0445 \u043f\u0440\u043e\u0442\u043e\u043a\u043e\u043b\u043e\u0432 \u043f\u043e \u043c\u043e\u0438\u043c \u0444\u0438\u043b\u044c\u0442\u0440\u0430\u043c","\u041d\u0430\u0441\u0442\u0440\u043e\u0439\u043a\u0430 \u043f\u0430\u0440\u0430\u043c\u0435\u0442\u0440\u043e\u0432 \u0440\u0430\u0441\u0441\u044b\u043b\u043a\u0438","\u043d\u043e\u0432\u044b\u0445 \u0442\u0435\u043d\u0434\u0435\u0440\u043e\u0432 \u043f\u043e \u043c\u043e\u0438\u043c \u0444\u0438\u043b\u044c\u0442\u0440\u0430\u043c","\u043a\u0430\u0436\u0434\u044b\u0435 2 \u0447\u0430\u0441\u0430","\u043a\u0430\u0436\u0434\u044b\u0439 \u0434\u0435\u043d\u044c","\u0447\u0435\u0440\u0435\u0437 \u0434\u0435\u043d\u044c","\u041d\u0430\u0437\u0432\u0430\u043d\u0438\u0435 \u0444\u0438\u043b\u044c\u0442\u0440\u0430","\u041f\u0435\u0440\u0438\u043e\u0434\u0438\u0447\u043d\u043e\u0441\u0442\u044c \u0440\u0430\u0441\u0441\u044b\u043b\u043a\u0438","\u041f\u043e\u0434\u043f\u0438\u0441\u043a\u0430 \u043d\u0430 \u0440\u0430\u0441\u0441\u044b\u043b\u043a\u0443"]}'); ?>