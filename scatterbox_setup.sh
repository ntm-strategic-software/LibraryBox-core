#!/bin/ash

# Update dir-generator.php
mv /opt/piratebox/www/dir-generator.php /opt/piratebox/www/dir-generator-old.php
cp /mnt/usb/LibraryBox/dir-generator.php /opt/piratebox/www/dir-generator.php

# Update php.ini
mv /etc/php.ini /etc/php-old.ini
cp /mnt/usb/scatterbox_setup_files/php.ini /etc/php.ini

echo All done!
