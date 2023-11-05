#!/bin/bash

# Fresh clone of repository
rm -rf deployment
git clone https://github.com/spainer/gallery.git deployment
rm -rf deployment/.git

# Install backend dependencies and copy config
cp deploy.env deployment/backend/.env
cd deployment/backend
composer install --optimize-autoloader --no-dev

# Build frontend and copy to backend
cd ../frontend
npm install
npm run build
cp dist/frontend/* ../backend/public/
mv ../backend/public/index.html ../backend/resources/views/index.php

# Cache routes and views (config seem to be only cacheable directly on the server)
cd ../backend
php artisan route:cache
php artisan view:cache
