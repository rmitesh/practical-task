## Practical Task

#### Requirement

- PHP v8.2.12

### Installation

- Install composer dependuncy.
```bash
composer install
```

- Copy `.env` file

```bash
cp .env.example .env
```

- Generate Application Key

```bash
php artisan key:generate
```

- Run migration
> Make sure you have created database `laravel_interview`.
```bash
php artisan migrate
```
