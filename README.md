# Berichtenapplicatie

Een berichtenapplicatie in Laravel 13. Account aanmaken, inloggen, berichten
plaatsen. Alleen de auteur kan een bericht bewerken of verwijderen.

## Draaien

```bash
composer install
npm install

cp .env.example .env
php artisan key:generate

touch database/database.sqlite
php artisan migrate --seed

npm run build
php artisan serve
```

Open `http://127.0.0.1:8000`.

Probeeraccount, komt mee met `migrate --seed`:

```
demo@bylore.test
portfolio123
```

## Tests

```bash
php artisan test
```

Achttien tests. De meeste daarvan kijken of een account niks bij andermans
berichten kan.
