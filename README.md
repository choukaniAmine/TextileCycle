# ReFashion – Laravel 12

Plateforme reliant particuliers, ateliers et associations pour donner une seconde vie aux vêtements
(dépôt, réparation, transformation, don). Module livré : **gestion des utilisateurs**.

## Installation
```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite        # déjà présent dans l'archive
php artisan migrate --seed
php artisan serve
```
Prérequis : PHP 8.2+, Composer. Aucun `npm` nécessaire (templates statiques dans `public/templates`).

## Comptes de démonstration (mot de passe : `password`)
| Rôle | E-mail |
|---|---|
| Admin | admin@refashion.test |
| Particulier | particulier@refashion.test |
| Atelier | atelier@refashion.test |
| Association | association@refashion.test |

## Templates
* **Front office** : Start Bootstrap *Landing Page* (MIT) – `public/templates/frontoffice` – layout `resources/views/layouts/front.blade.php`
  * Source : https://startbootstrap.com/theme/landing-page · https://github.com/StartBootstrap/startbootstrap-landing-page
* **Back office** : AdminLTE 3.2 (MIT) – `public/templates/backoffice` – layout `resources/views/layouts/admin.blade.php`
  * Source : https://adminlte.io · https://github.com/ColorlibHQ/AdminLTE · doc : https://adminlte.io/docs/3.2/
* Dépendances chargées par CDN : Bootstrap 5.2 + Bootstrap Icons (front), jQuery 3.6 + Bootstrap 4.6 + Font Awesome 5 (back).

## Structure
* `app/Enums/UserRole.php` – rôles (admin, particulier, atelier, association)
* `app/Http/Middleware/EnsureUserRole.php` – alias `role:admin`
* `app/Http/Controllers/Auth|Front|Admin` – authentification, profil, CRUD utilisateurs
* `routes/web.php` – front (`/`), auth, back office (`/admin`)
