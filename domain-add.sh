#!/bin/sh
if [ -z "$1" ]
  then
    echo "Введите название домена"
    exit
fi
sudo sed -i "s/server_name /server_name $1\n/" /etc/nginx/sites/semantic.conf
sudo nginx -t && sudo service nginx reload
php artisan domain:add $1
sudo chown -R www-data storage
sudo chmod a+rw -R storage
php artisan migrate --domain=$1

