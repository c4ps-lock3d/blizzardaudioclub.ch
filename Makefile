deploy:
	rsync -avz public/themes/shop/store/build infomaniakbac:~/sites/blizzardaudioclub.ch/public/themes/shop/store
	ssh infomaniakbac 'cd ~/sites/blizzardaudioclub.ch && git pull origin master && make install'

install: vendor/autoload.php .env public/storage
	php artisan vendor:publish --provider="Webkul\Store\Providers\StoreServiceProvider" --force
	php artisan optimize
	php artisan cache:clear

.env:
	cp .env.example
	php artisan key:generate

public/storage:
	php artisan storage:link

vendor/autoload.php: composer.lock
	composer install
	composer dunp-autoload
	touch vendor/autoload.php

public/build/manifest.json: package.json
	npm i
	npm run build