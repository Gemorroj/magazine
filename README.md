# Магазин

- Использует php 7 как бэкенд (Symfony 4)
- И Vue.js + element-ui как фронтенд


- Сервер должен перенаправлять все запросы на `public/index.html`
- Если запрос начинается с `/api/public/` или `/api/private/`, то направлять на `api.php`

- Фотографии хранятся на серввере https://apidocs.imgur.com/ (php прослойка https://github.com/j0k3r/php-imgur-api-client)
- Превью фото делается через сервис https://rethumb.com/api
- БД `mongo` находится на стороннем сервие https://mlab.com/databases/magazine


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
    gzip_types      text/css application/json application/javascript text/html application/xhtml+xml application/xml text/xml application/rss+xml text/plain;

    client_max_body_size 55m;
    server_tokens off;
    proxy_read_timeout 90;
    proxy_connect_timeout 90;

    upstream phpfcgi {
        # server 127.0.0.1:9000;
        # server unix:/run/php5-fpm.sock; #for PHP-FPM running on UNIX socket
        server unix:/run/php-fpm.sock;
    }

    include /etc/nginx/conf.d/*.conf;
}

server {
    location ~ /\. {
        deny all;
    }
    location ~ \.php$ {
        return 404;
    }

    charset utf-8;
    listen 80;

    server_name magazine.wapinet.ru;
    root /var/www/magazine/web;

    error_log /var/log/nginx/magazine.error.log;
    access_log /var/log/nginx/magazine.access.log;


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
        fastcgi_pass phpfcgi;
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
