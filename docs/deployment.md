# Deployment

The demo is a **full Laravel application**, not a static export, so it needs a PHP host. It cannot
be served from object storage the way the HTML edition's preview is.

## Server requirements

| | |
|---|---|
| PHP | 8.3+ with `pdo`, `mbstring`, `openssl`, `ctype`, `fileinfo` |
| Composer | 2.x |
| Node.js | 18+ (build step only) |
| Web server | Nginx or Apache, document root **must** be `public/` |
| Database | SQLite is fine for a demo; MySQL or Postgres for anything heavier |

## Stand up a preview

```bash
composer create-project laravel/laravel gentelella-preview
cd gentelella-preview
composer require colorlibhq/gentelella-laravel

php artisan gentelella:install
npm install gentelella@^4.1 sass
# add resources/js/gentelella.js to vite.config.js, then:
npm ci && npm run build
```

Turn the showcase on in `config/gentelella.php`:

```php
'demo' => true,
'auth' => ['enabled' => true, 'home' => '/demo'],
```

```bash
php artisan migrate
php artisan gentelella:demo
```

## Production settings

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Route caching is safe here: the package registers its routes in a `booted()` callback, which runs
before `route:cache` collects them.

Set `APP_DEBUG=false`. The demo seeds a public dataset, but the auth screens are real — create a
known demo account deliberately rather than leaving registration open on a public host, or set
`'auth' => ['register' => false]`.

## Nginx

```nginx
server {
    listen 443 ssl http2;
    server_name <host>;

    root /var/www/<name>/public;
    index index.php;

    location / { try_files $uri $uri/ /index.php?$query_string; }

    location ~ \.php$ {
        fastcgi_pass unix:/run/<name>/<name>.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* { deny all; }
}
```

Serve `php-fpm` over a unix socket under `/run/<name>/`, and keep the pool in its own systemd unit
so a restart does not touch anything else on the box.

## If the demo is embedded in an iframe

A product page that previews the demo in an iframe needs the origin to allow it. Check what the
host actually sends:

```bash
curl -sI https://<host>/ | grep -iE "x-frame|content-security"
```

Expected: no `X-Frame-Options: DENY`, and either no `frame-ancestors` directive or one that names
the embedding origin. Add it in middleware or at the vhost — remembering that nginx's `add_header`
replaces rather than merges, so an app-sent CSP has to be hidden first with
`proxy_hide_header Content-Security-Policy`.

## Deciding the hostname

The HTML edition previews under Colorlib's own preview host. This edition needs a PHP host instead,
so it wants its own name — a subdomain, or a path on a box that already runs PHP. That is a
decision to make before the first deploy, because it ends up in `docs_url`, in the README badges
and in any link that gets shared.
