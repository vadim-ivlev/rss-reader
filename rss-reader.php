<?php
/**
 * Скрипт читает RSS-ленту и отправляет электронные письма о новых элементах (items)
 * получателям.
 *
 * Список получателей рассылки находится в файле rss-emails.txt.
 *
 * У новых элементов rss время публикации больше больше времени 
 * сохраненного в rss-last-timestamp.txt.
 * 
 * Лог рассылок находится в rss-mailing.log
 *
 */

// url где находится RSS
$rss_url = 'https://rg.ru/tema/mir/rss.xml';
// файл со списком email адресов рассылки. В каждой строчке один адрес.
$emails_file = 'rss-emails.txt';
// файл со штампом даты времени, последней считанной записи
$last_timestamp_file = 'rss-last-timestamp.txt';
// лог файл
$log_file = 'rss-mailing.log';


$max_timestamp = $last_timestamp = @intval(file_get_contents( dirname(__FILE__) . '/' . $last_timestamp_file)); 
$emails = get_emails(dirname(__FILE__) .  '/' . $emails_file);
$rss = new SimpleXMLElement(file_get_contents($rss_url));

// debug
// $last_timestamp = 1581496920;

// формируем текст письма выбирая елементы появившиеся позже последней считанной записи
$mail_body = "";
foreach ($rss->channel->item as $item){
    $pubDate = $item->pubDate;
    $item_timestamp = strtotime($pubDate);
    if ($item_timestamp > $last_timestamp) {
        $max_timestamp = max($max_timestamp, $item_timestamp);
        $id = $item['id'];
        $title = $item->title;
        $link = $item->link;
        $mail_body .= render($id, $pubDate, $title, $link);
    }
}
// Если новых записей нет выходим
if ($mail_body == "") exit;


// Для отправки HTML-письма должен быть установлен заголовок Content-type
$headers  = "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html; charset=utf8\r\n";
$headers .= "From: noreply@rg.ru\r\n";

mail($emails, 'Новые материалы раздела "В Мире" на сайте rg.ru', $mail_body, $headers);
file_put_contents(dirname(__FILE__) . '/' . $last_timestamp_file, $max_timestamp);
file_put_contents(dirname(__FILE__) . '/' . $log_file, date("Y-m-d H:i") .PHP_EOL , FILE_APPEND | LOCK_EX);



// F U N C T I O N S  ---------------------------------------------------------------------

// get_emails формирует список рассылки
function get_emails($file) {
    $text = file_get_contents($file);
    $str = trim(preg_replace('/\s+/',' ', $text));
    $list = explode(' ', $str);
    return implode(', ', $list);
}

// render формирует участок письма
function render($id, $pubDate, $title, $link) {
    $str = <<<STR
    <html>
        <head>
            <title>$title</title>
        </head>
        <body>    
            <p>
            <span style="color:#888;">$pubDate</span><br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <a href="$link" >$title</a>
            ($id)
            <p> 
        </body>
    </html>    

STR;
    return $str;    
}


