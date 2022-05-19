for domain in $(php artisan domain:list | grep Domain: | cut -d" " -f2); do
    echo ==========$domain===========
    php artisan cats:norm --domain=$domain
done
