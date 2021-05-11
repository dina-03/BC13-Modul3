<?php
$text = "Имя: ".$_POST["name"]."<br>Телефон: ".$_POST["phone"]."<br><br>";
						
function get_data($smtp_conn)
{
    $data="";
    while($str = fgets($smtp_conn,515))
    {
        $data .= $str;
        if(substr($str,3,1) == " ") { break; }
    }
    return $data; 
}

$smtpserver="smtp.mandrillapp.com"; // адрес smtp-сервера   +++
$smtpport="587"; // порт smtp-сервера   +++
$maillogin="adres@mail.ru"; // Логин smtp   +++
$mailpass="12345"; // Пароль smtp   +++

$senderdomain="mail.ru"; // Домен отправителя   +++

$sendermail="dingrafikweb@mail.ru"; // Адрес отправителя   +++
$replymail=$_POST["email"]; // Адрес для ответа

$recepname="Dina"; // Имя получателя

$recepmail="dingrafikweb@mail.ru"; // Куда отправлять заявки (наш email-адрес)   +++

$sendername=$_POST["name"]; // Имя отправителя
$replyname=$_POST["name"]; // Имя для ответа

$mailsubject="Subject"; // Тема письма

$header="Date: ".date("D, j M Y G:i:s")." +0700\r\n";
$header.="From: =?utf-8?Q?".str_replace("+","_",str_replace("%","=",urlencode($sendername)))."?= <".$sendermail.">\r\n";
$header.="X-Mailer: The Bat! (v3.99.3) Professional\r\n";
$header.="Reply-To: =?utf-8?Q?".str_replace("+","_",str_replace("%","=",urlencode($replyname)))."?= <".$replymail.">\r\n";
$header.="X-Priority: 3 (Normal)\r\n";
$header.="Message-ID: <172562218.".date("YmjHis").">\r\n";
$header.="To: =?utf-8?Q?".str_replace("+","_",str_replace("%","=",urlencode($recepname)))."?= <".$recepmail.">\r\n";
//если нужно слать копию, то добавьте: $header.="Cc: =?utf-8?Q?".str_replace("+","_",str_replace("%","=",urlencode($recepname)))."?= <ya@saitos.ru>\r\n"; 
$header.="Subject: =?utf-8?Q?".str_replace("+","_",str_replace("%","=",urlencode($mailsubject)))."?=\r\n";

$header.="MIME-Version: 1.0\r\n";
$header.="Content-Type: text/html; charset=utf-8\r\n";
$header.="Content-Transfer-Encoding: 8bit\r\n";

$file=fopen('maillog.txt', 'a');

$smtp_conn = fsockopen($smtpserver, $smtpport,$errno, $errstr, 10);
if(!$smtp_conn) {$current .= $recepmail." / ".$_POST["phone"]." / ".$_POST["name"]." / ".$_POST["email"]." соединение с сервером не прошло\r\n"; fwrite($file, $current); fclose($smtp_conn); exit;}
$data = get_data($smtp_conn);
fputs($smtp_conn,"EHLO ".$senderdomain."\r\n");
$code = substr(get_data($smtp_conn),0,3);
if($code != 250) {$current .= $recepmail." / ".$_POST["phone"]." / ".$_POST["name"]." / ".$_POST["email"]." ошибка приветсвия EHLO\r\n"; fwrite($file, $current); fclose($smtp_conn); exit;}
fputs($smtp_conn,"AUTH LOGIN\r\n");
$code = substr(get_data($smtp_conn),0,3);
if($code != 334) {$current .= $recepmail." / ".$_POST["phone"]." / ".$_POST["name"]." / ".$_POST["email"]." сервер не разрешил начать авторизацию\r\n"; fwrite($file, $current); fclose($smtp_conn); exit;}

fputs($smtp_conn,base64_encode($maillogin)."\r\n");
$code = substr(get_data($smtp_conn),0,3);
if($code != 334) {$current .= $recepmail." / ".$_POST["phone"]." / ".$_POST["name"]." / ".$_POST["email"]." ошибка доступа к такому юзеру\r\n"; fwrite($file, $current); fclose($smtp_conn); exit;}

fputs($smtp_conn,base64_encode($mailpass)."\r\n");
$code = substr(get_data($smtp_conn),0,3);
if($code != 235) {$current .= $recepmail." / ".$_POST["phone"]." / ".$_POST["name"]." / ".$_POST["email"]." не правильный пароль\r\n"; fwrite($file, $current); fclose($smtp_conn); exit;}

$size_msg=strlen($header."\r\n".$text);

fputs($smtp_conn,"MAIL FROM:<".$sendermail."> SIZE=".$size_msg."\r\n");
$code = substr(get_data($smtp_conn),0,3);
if($code != 250) {$current .= $recepmail." / ".$_POST["phone"]." / ".$_POST["name"]." / ".$_POST["email"]." сервер отказал в команде MAIL FROM\r\n"; fwrite($file, $current); fclose($smtp_conn); exit;}

fputs($smtp_conn,"RCPT TO:<".$recepmail.">\r\n");
$code = substr(get_data($smtp_conn),0,3);
if($code != 250 AND $code != 251) {$current .= $recepmail." / ".$_POST["phone"]." / ".$_POST["name"]." / ".$_POST["email"]." Сервер не принял команду RCPT TO\r\n"; fwrite($file, $current); fclose($smtp_conn); exit;}

fputs($smtp_conn,"DATA\r\n");
$code = substr(get_data($smtp_conn),0,3);
if($code != 354) {$current .= $recepmail." / ".$_POST["phone"]." / ".$_POST["name"]." / ".$_POST["email"]." сервер не принял DATA\r\n"; fwrite($file, $current); fclose($smtp_conn); exit;}

fputs($smtp_conn,$header."\r\n".$text."\r\n.\r\n");
$code = substr(get_data($smtp_conn),0,3);
if($code != 250) {$current .= $recepmail." / ".$_POST["phone"]." / ".$_POST["name"]." / ".$_POST["email"]." "Mail unsuccessful"\r\n"; fwrite($file, $current); fclose($smtp_conn); exit;}

fputs($smtp_conn,"QUIT\r\n");
$current .= $recepmail." / ".$_POST["phone"]." / ".$_POST["name"]." / ".$_POST["email"]." -"Mail successful!"\r\n";
fwrite($file, $current); fclose($smtp_conn);

echo "<br>".$message;
?>