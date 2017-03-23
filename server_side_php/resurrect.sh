#!/bin/sh
SERVICE='mail_leaker.php'
 
if ps ax | grep -v grep | grep $SERVICE > /dev/null
then
#    echo "$SERVICE service running, everything is fine"
else
#    echo "$SERVICE is not running"
#    echo "$SERVICE is not running!" | mail -s "$SERVICE down" inkpalne
    rm -f /home3/inkpalne/public_html/dealtracker/gen/mail_leak.loc
    php-cli  /home3/inkpalne/public_html/dealtracker/mail_leaker.php &
fi