# I will not provide any help, so don't bother opening an issue because it will be ignored.


# Recommanded specs:

Debian 8
2GB Ram
20GB HDD/SSD


# Commands list:

1- apt-get update && apt-get upgrade
2- apt-get install apache2 mysql-server php5 phpmyadmin curl
3- ln -s /usr/share/phpmyadmin /var/www/html
4- curl -sL https://deb.nodesource.com/setup_6.x -o nodesource_setup.sh
5- apt-get install nodejs
6- mkdir /var/report-bot
7- cd /var/report-bot
8- After having the server_files uploaded into this directory, run this 
9- chmod -R 777 * && npm install


10-crontab -e (then enter the following)

*/1 * * * * /usr/bin/php /var/www/html/inc/reported_checker.php
0 */6 * * * cd /var/www/html/inc/rewards && /usr/bin/php rewards_checker_tag.php

