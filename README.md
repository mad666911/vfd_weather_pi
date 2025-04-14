# vfd_weather_pi
Display time, weather and custom message on a VFD display using a Raspberi PI

#PI image:
Install Raspbian lite 32bit headless image
Configure with your wifi and setup a password
Enable SSH

#Install LAMP
sudo apt update && sudo apt upgrade -y
sudo apt install apache2 php mariadb-server php-mysql -y

#Install usb serial driver:
sudo depmod -a
sudo modprobe usbserial
sudo modprobe pl2303

#Test driver:
lsmod | grep pl2303

#Setup dialup permission:
sudo usermod -aG dialout $USER

#Setup cron job:
crontab -e
*/10 * * * * php /var/www/html/fetch_weather.php
* * * * * /bin/bash -c 'php /var/www/html/vfd_weather.php; sleep 20; php /var/www/html/vfd_weather.php; sleep 20; php /var/www/html/vfd_weather.php'

#Copy the files:
/var/www/html/vfd_weather.php
/var/www/html/fetch_weather.php
/var/www/html/custom_messages.txt

#Config:
Etit the 2 php file header with correct timezone and postal or zip code
Edit custom_messages.txt with your own message

#1st time weather init:
php /var/www/html/fetch_weather.php
