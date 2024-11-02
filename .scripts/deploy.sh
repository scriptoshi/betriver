#!/bin/bash
set -e
echo "Deployment started ..."
echo "# checkout main version of the app"
cd /var/www/build && git checkout main
echo "# Install composer dependencies"
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
echo "# Install & Compile npm assets"
npm i && npm run build
echo "build complete!"
echo "# Run fresh database migrations and seed"
php artisan migrate --force
#php artisan db:seed --force
echo "Swapping Folders"
rm -rf /var/www/demo_betn_io_back
mv /var/www/demo.betn.io /var/www/demo_betn_io_back
mv /var/www/demo /var/www/demo.betn.io
echo "# Run post instalation setup"
cd /var/www/demo.betn.io && php artisan clear-compiled
cd /var/www/demo.betn.io && php artisan optimize
cd /var/www/demo.betn.io && php artisan storage:link
cd /var/www/demo.betn.io && sudo chgrp -R www-data storage bootstrap/cache
cd /var/www/demo.betn.io && sudo chmod -R ug+rwx storage bootstrap/cache
echo "Betn Deployment Finished"
