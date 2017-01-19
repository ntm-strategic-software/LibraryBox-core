#!/bin/ash

# Update dir-generator.php
mv /opt/piratebox/www/dir-generator.php /opt/piratebox/www/dir-generator-old.php
cp /mnt/usb/LibraryBox/dir-generator.php /opt/piratebox/www/dir-generator.php

# Update php.ini
mv /etc/php.ini /etc/php-old.ini
cp /mnt/usb/LibraryBox/php.ini /etc/php.ini

# Clear Shared folder
rm -Rf /mnt/usb/LibraryBox/Shared
mkdir /mnt/usb/LibraryBox/Shared

# Rename the old Content folder
mv -f /mnt/usb/LibraryBox/Content /mnt/usb/LibraryBox/Content-old

# Add the new Content folder
cp /mnt/usb/scatterbox_setup_files/Content /mnt/usb/LibraryBox/Content

echo All done!
