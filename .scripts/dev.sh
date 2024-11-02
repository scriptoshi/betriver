#!/bin/bash
set -e
echo "Deployment started ..."
echo "# checkout dev version of the app"
git checkout dev
echo "# Install composer dependencies"
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
echo "# Install & Compile npm assets"
npm i && npm run build
echo "build complete!"
echo "# Run fresh database migrations and seed"
php artisan migrate --force
#php artisan db:seed --force
echo "Swapping Folders"
rm -rf /var/www/dev_betn_io_back
mv /var/www/dev.betn.io /var/www/dev_betn_io_back
mv /var/www/dev /var/www/dev.betn.io
echo "# Run post instalation setup"
cd /var/www/dev.betn.io && php artisan clear-compiled
cd /var/www/dev.betn.io && php artisan optimize
cd /var/www/dev.betn.io && php artisan storage:link
cd /var/www/dev.betn.io && sudo chgrp -R www-data storage bootstrap/cache
cd /var/www/dev.betn.io && sudo chmod -R ug+rwx storage bootstrap/cache
echo "Dev Deployment Finished"
