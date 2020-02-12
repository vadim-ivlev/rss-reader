RSS-reader
=========

Скрипт читает RSS-ленту https://rg.ru/tema/mir/rss.xml 
и отправляет электронные письма о новых элементах (items)
получателям.

Список получателей рассылки находится в файле `rss-emails.txt`.

У новых элементов rss время публикации больше больше времени 
сохраненного в `rss-last-timestamp.txt`. Файл обновляется
после каждой успешной рассылки.
 
Лог рассылок находится в `rss-mailing.log`.



**Запуск**

    php rss-reader.php


**Деплой**

Скрипт размещен на сервере `works`, в папке `/var/www/works/www/scripts/rss-reader/`

`crontab` запускает скрипт каждые 5 мин с параметрами:

    */5 * * * *  php /var/www/works/www/scripts/rss-reader/rss-reader.php

Измененние параметров `crontab`

    crontab -e

