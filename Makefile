deploy:
	rsync -avz public/themes/shop/store/build infomaniakbac:~/sites/blizzardaudioclub.ch/public/themes/shop/store
	ssh infomaniakbac 'cd ~/sites/blizzardaudioclub.ch && git pull origin master && make install'

install: vendor/autoload.php .env public/storage
	php artisan vendor:publish --provider="Webkul\Store\Providers\StoreServiceProvider" --force
	php artisan cache:clear


public/storage:
	php artisan storage:link