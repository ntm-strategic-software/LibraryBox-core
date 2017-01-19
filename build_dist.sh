#!/bin/bash

# make the dist folder
rm -rf dist
mkdir dist

# copy over the procedure file
cp procedure.txt dist/

# make the Content folder and copy over files
mkdir dist/scatterbox_setup_files
mkdir dist/scatterbox_setup_files/Content
cp -R LibraryBox-landingpage/www_content/* dist/scatterbox_setup_files/Content/
mkdir dist/scatterbox_setup_files/Content/www_librarybox
cp -R customization/www_librarybox/* dist/scatterbox_setup_files/Content/www_librarybox/

# copy over scripts
cp scatterbox_setup.sh dist/scatterbox_setup_files/
cp php.ini dist/scatterbox_setup_files/
cp dir-generator.php dist/scatterbox_setup_files/

echo All done!
