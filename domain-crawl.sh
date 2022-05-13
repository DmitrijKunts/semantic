for domain in $(php artisan domain:list | grep Domain: | cut -d" " -f2); do
    echo ==========$domain===========
    php artisan stat --domain=$domain
    php artisan cats:crawl --domain=$domain
done
