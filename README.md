# Магазин

- Использует php 8.2 как бэкенд (Symfony 6.4)
- БД Sqlite
- И Vue.js + element-ui как фронтенд

---

- Сервер должен перенаправлять все запросы на `public/index.html`
- Если запрос начинается с `/api/public/` или `/api/private/`, то направлять на `api.php`


### Установка
```bash
git clone https://github.com/Gemorroj/magazine.git
cd magazine
cp .env.dist .env
composer install --no-dev --optimize-autoloader --apcu-autoloader
rm -rf ./var/cache/*
rm -rf ./var/log/*
service php-fpm restart
```


### Установка прав доступа на запись:
- `var/log`
- `var/cache`
- `public/upload`

### Установка БД (var/data.db)
```bash
php bin/console doctrine:database:create
```
Можно залить фикстуры (dev и test окружение)
```bash
php bin/console doctrine:fixtures:load
```


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

    ssl_protocols TLSv1.2 TLSv1.3;
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
        expires 7d;
    }
    location /bundles/ {
        access_log off;
        expires 7d;
    }
    location /upload/ {
        access_log off;
        expires 7d;
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
