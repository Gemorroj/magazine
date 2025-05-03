# Магазин

- Использует PHP 8.4 как бэкенд (Symfony 7.2)
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
add-apt-repository ppa:ondrej/nginx
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
apt install nginx
systemctl enable nginx
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


### Конфигурация nginx:
Заменить example.com на актуальный домен
```bash
echo 'server {
    listen 80;

    server_name example.com www.example.com;
	return 301 https://$server_name$request_uri;
}

server {
    location ~ /\.well-known\/acme-challenge {
        allow all;
    }
    location ~ /\. {
        deny all;
    }
    location ~ \.php$ {
        return 404;
    }

    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_certificate /root/.acme.sh/example.com/fullchain.cer;
    ssl_certificate_key /root/.acme.sh/example.com/example.com.key;

    charset utf-8;
    listen 443 ssl http2;

    server_name example.com www.example.com;
    root /var/www/magazine/public;

    error_log /var/log/nginx/magazine.error.log;
    access_log /var/log/nginx/magazine.access.log;

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
}' > /etc/nginx/sites-available/example.com.conf
ln -s /etc/nginx/sites-available/example.com.conf /etc/nginx/sites-enabled/example.com.conf
```
