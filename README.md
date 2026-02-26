# Магазин

- Использует PHP 8.4 как бэкенд (Symfony 8.0)
- БД Sqlite
- И Vue.js + element-ui как фронтенд

---

- Сервер должен перенаправлять все запросы на `public/index.html`
- Если запрос начинается с `/api/public/` или `/api/private/`, то направлять на `api.php`
- OpenAPI документация по адресам http://example.com/api/public/doc и http://example.com/api/private/doc


### Базовая установка (актуально для Ubuntu 24.04)
```bash
apt update && sudo apt dist-upgrade && sudo apt autoremove --purge
apt install software-properties-common
add-apt-repository ppa:ondrej/php
curl -o /etc/apt/trusted.gpg.d/angie-signing.gpg https://angie.software/keys/angie-signing.gpg
echo "deb https://download.angie.software/angie/$(. /etc/os-release && echo "$ID/$VERSION_ID $VERSION_CODENAME") main" | sudo tee /etc/apt/sources.list.d/angie.list > /dev/null
apt update && apt dist-upgrade
hostnamectl set-hostname magazine
timedatectl set-timezone UTC

# edit /etc/hosts to associate domain to ip address without dns requests. see https://www.linode.com/docs/guides/getting-started/#update-your-systems-hosts-file
# edit /etc/ssh/sshd_config - set `Port 2200`
# edit /root/.ssh/authorized_keys - add public key
reboot
```

```bash
apt install htop mc git unzip
apt install angie
systemctl enable angie
apt install php8.4-fpm php8.4-curl php8.4-gd php8.4-intl php8.4-mbstring php8.4-xml php8.4-zip php8.4-apcu php8.4-sqlite3
```

### Установка
```bash
cd /var/www
curl -L -o composer.phar https://getcomposer.org/download/latest-stable/composer.phar
chmod 755 composer.phar

git clone https://github.com/Gemorroj/magazine.git
cd magazine
cp .env.dist .env
# edit .env
../composer.phar install --no-dev --optimize-autoloader --apcu-autoloader
rm -rf ./var/cache/*
rm -rf ./var/log/*
service php-fpm restart

cd /var/www/magazine
chmod 777 ./var/log
chmod 777 ./var/cache
chmod 777 ./public/upload

php bin/console doctrine:database:create
chmod 666 ./var/data.db

# fixtures for dev
php bin/console doctrine:fixtures:load
```

### Конфигурация php-fpm:
```
# edit /etc/php/8.4/fpm/pool.d/www.conf
# listen.allowed_clients = 127.0.0.1
# pm.status_path = /statusfpm
# listen = /run/php/php8.4-fpm.sock
# edit pm.* settings for performance
```

### Конфигурация angie:
Заменить example.com на актуальный домен
```bash
# edit /etc/angie/angie.conf
# user  www-data;
# server_tokens off;
# gzip  on;
# gzip_comp_level 2;
# gzip_min_length 40;
# gzip_types text/css text/plain application/json text/javascript application/javascript text/xml application/xml application/xml+rss application/x-font-ttf application/x-font-opentype application/vnd.ms-fontobject image/svg+xml image/x-icon font/ttf font/opentype;
# resolver 127.0.0.53;
# acme_client magazine_acme_client https://acme-v02.api.letsencrypt.org/directory;
# server {
#     charset utf-8;
#     listen 80;
#     server_name  localhost;
#     access_log off;
# 
# 	location = /statusfpm {
# 		fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
# 		include fastcgi_params;
# 		fastcgi_pass unix:/run/php/php8.4-fpm.sock;
# 		allow 127.0.0.1;
# 		deny  all;
#     }
#     location /status/ {
#         api /status/;
#         allow 127.0.0.1;
#         deny  all;
# 	}
# }
```

```bash
echo 'server {
    listen 80;
    listen [::]:80;

    server_name example.com www.example.com;
	return 301 https://$server_name$request_uri;
}

server {
    location ~ /\. {
        deny all;
    }
    location ~ \.php$ {
        return 404;
    }

    ssl_protocols TLSv1.2 TLSv1.3;
    acme magazine_acme_client;
    ssl_certificate $acme_cert_magazine_acme_client;
    ssl_certificate_key $acme_cert_key_magazine_acme_client;
    ssl_session_timeout 1h;
    ssl_session_cache shared:SSL:10m;

    charset utf-8;
    listen 443 ssl;
    listen [::]:443 ssl;
    http2 on;

    server_name example.com www.example.com;
    root /var/www/magazine/public;

    error_log /var/log/angie/magazine.error.log;
    access_log /var/log/angie/magazine.access.log;

    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains";
    add_header X-Frame-Options "DENY";
    add_header X-Content-Type-Options nosniff;

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
    location /bundles/ {
        access_log off;
        expires 30d;
    }
    location /build/ {
        access_log off;
        expires 30d;
    }
    location /upload/ {
        access_log off;
        expires 7d;
    }

    # JSON api
    location ~ ^/api/(public|private)/ {
        fastcgi_pass unix:/run/php/php8.4-fpm.sock;
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
}' > /etc/angie/http.d/example.com.conf
```
