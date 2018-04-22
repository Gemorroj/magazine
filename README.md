# Магазин

- Использует php 7 как бэкенд (Symfony 4)
- БД Mysql (или Mariadb)
- И Vue.js + element-ui как фронтенд


- Сервер должен перенаправлять все запросы на `public/index.html`
- Если запрос начинается с `/api/public/` или `/api/private/`, то направлять на `api.php`

- Фотографии хранятся на серввере https://apidocs.imgur.com/ (php прослойка https://github.com/j0k3r/php-imgur-api-client). 1250 загрузок в месяц, 12500 скачиваний в месяц.
- Превью фото делается через сервис https://rethumb.com/api максимальный размер картинки 512кб

### План по переделке (выбранный вариант, на данный момент)
- Основную БД перевести на sqlite (чтобы упростить инфраструктуру, т.е. отвязаться от промышленных БД, требующих запущенного сервера). Пока что в доктрине нет поддержки `foreign keys` для sqlite - https://github.com/doctrine/dbal/issues/2833
- Фотки хранить на яндекс диске - https://github.com/jack-theripper/yandex
- Превью пока не понятно как делать


### Мысли по переделке
- Возможно, лучше использовать возможности vk по работае с товарами, для хранения базы данных товаров, включая фотографии
    - https://vk.com/dev/market
    - https://vk.com/dev/market.getCategories?params[count]=1000&params[v]=5.74
    - https://vk.com/dev/upload_files_2?f=6.%2B%D0%97%D0%B0%D0%B3%D1%80%D1%83%D0%B7%D0%BA%D0%B0%2B%D1%84%D0%BE%D1%82%D0%BE%D0%B3%D1%80%D0%B0%D1%84%D0%B8%D0%B8%2B%D0%B4%D0%BB%D1%8F%2B%D1%82%D0%BE%D0%B2%D0%B0%D1%80%D0%B0
    
- Возможно, использовать api яндекса (фоотки, диск, datasync, market ???)
    - https://tech.yandex.ru/datasync/http/
    - https://github.com/nixsolutions/yandex-php-library
    - https://github.com/jack-theripper/yandex



### Установка прав доступа на запись:
- `var/log`
- `var/cache`


### Конфигурация nginx:
```nginx
user  nginx;
worker_processes  auto;

error_log  /var/log/nginx/error.log warn;
pid        /var/run/nginx.pid;


events {
    use epoll;
    worker_connections  1024;
}


http {
    fastcgi_read_timeout 30;
    include       /etc/nginx/mime.types;
    default_type  application/octet-stream;

    log_format  main  '$remote_addr - $remote_user [$time_local] "$request" '
                      '$status $body_bytes_sent "$http_referer" '
                      '"$http_user_agent" "$http_x_forwarded_for"';

    access_log  /var/log/nginx/access.log  main;

    sendfile        on;
    tcp_nodelay on;
    tcp_nopush on;

    keepalive_timeout  30;

    gzip  on;
    gzip_comp_level 2;
    gzip_min_length 40;
    gzip_types      text/css application/json application/javascript application/xhtml+xml application/xml text/xml application/rss+xml text/plain;

    client_max_body_size 55m;
    server_tokens off;
    proxy_read_timeout 90;
    proxy_connect_timeout 90;

    include /etc/nginx/conf.d/*.conf;
}

server {
    location ~ /\.well-known\/acme-challenge {
        allow all;
    }
    location ~ /\. {
        deny all;
    }

    ssl on;
    ssl_protocols TLSv1.1 TLSv1.2;
    ssl_certificate /path_to_fullchain.pem;
    ssl_certificate_key /path_to_key.pem;
    ssl_trusted_certificate /path_to_chain.pem;

    charset utf-8;
    listen 443 ssl http2;

    server_name magazine.wapinet.ru;
    root /var/www/magazine/public;

    error_log /var/log/nginx/magazine.error.log;
    access_log /var/log/nginx/magazine.access.log;

    # todo: Content-Security-Policy
    add_header Strict-Transport-Security "max-age=31536000";
    add_header X-Frame-Options "DENY";

    # Кэширование
    location = /favicon.ico {
        access_log off;
        expires 30d;
    }
    location = /robots.txt {
        access_log off;
        expires 30d;
    }
    location = /apple-touch-icon.png {
        access_log off;
        expires 30d;
    }
    location /build/ {
        access_log off;
        expires 30d;
    }

    # JSON api
    location ~ ^/api/(public|private)/ {
        fastcgi_pass unix:/run/php-fpm.sock;
        include fastcgi_params;

        fastcgi_param SCRIPT_FILENAME $realpath_root/api.php;
        fastcgi_param DOCUMENT_ROOT $realpath_root;

        #internal;
    }

    # index
    location / {
        # try to serve file directly, fallback to index.html
        try_files $uri /index.html;
    }
}
```
