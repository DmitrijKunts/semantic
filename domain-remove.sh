#!/bin/sh
if [ -z "$1" ]
  then
    echo "Введите название домена"
    exit
fi
sudo sed -i "s/$1/ \n/" /etc/nginx/sites/semantic.conf
sudo nginx -t && sudo service nginx reload
# echo "Removin public/imgs/$1"
# rm -rf public/imgs/$1
php artisan domain:remove $1 --force
