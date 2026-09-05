# AGENTS.md

## Проект
Магазин. PHP 8.5 (Symfony 8.1) бэкенд, SQLite БД, Vue.js 2 + Element UI фронтенд (Webpack).

## Ключевые факты
- Все HTTP-запросы перенаправляются на `public/index.html`; запросы `/api/public/` и `/api/private/` — на `public/api.php`.
- API документация (Nelmio): `/api/public/doc` и `/api/private/doc`.
- PHP-код в `src/` (PSR-4 `App\`), тесты в `tests/` (`App\Tests\`), миграции Doctrine в `migrations/`.

## Команды
```bash
composer install          # установка PHP-зависимостей
yarn install              # установка JS-зависимостей
php bin/console           # Symfony CLI
yarn dev-build            # сборка фронтенда (development)
yarn prod-build           # сборка фронтенда (production)
```
