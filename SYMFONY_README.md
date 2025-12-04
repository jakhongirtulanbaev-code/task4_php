# Task 4 - Symfony User Management App

Bu loyiha **Symfony 6.4** framework'i bilan yaratilgan va vazifa shartlariga 100% mos keladi.

## Talablar

- PHP 8.1+
- Composer
- MySQL 8+ yoki PostgreSQL

## O'rnatish

1. **Composer dependencies o'rnatish:**

```bash
cd C:\OSPanel\home\task4
composer install
```

2. **Environment sozlash:**

`.env` faylini ochib, database ma'lumotlarini o'zgartiring:

```env
DATABASE_URL="mysql://root:jonymysql@127.127.126.4:3306/task4_php?serverVersion=8.4&charset=utf8mb4"
```

3. **Database yaratish va migration:**

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

Yoki mavjud database'da:

```bash
php bin/console doctrine:migrations:migrate
```

4. **Server ishga tushirish:**

```bash
php bin/console server:start
```

Yoki OSPanel'da `http://task4` orqali kirish.

## Funksiyalar

- ✅ Registratsiya (email + password)
- ✅ Email tasdiqlash (async)
- ✅ Login/Logout
- ✅ User Management (jadval, toolbar, multiple selection)
- ✅ Block/Unblock/Delete actions
- ✅ Har bir requestda user status tekshiruvi
- ✅ Unique index database'da
- ✅ Professional UI (Bootstrap 5)

## Struktura

- `src/Entity/User.php` - User entity
- `src/Controller/` - Controllers
- `src/Service/EmailService.php` - Email yuborish
- `src/EventSubscriber/UserStatusSubscriber.php` - Har requestda tekshiruv
- `templates/` - Twig templates
- `migrations/` - Database migrations

## Toolbar Actions

1. **Block** (button with text) - Userlarni bloklash
2. **Unblock** (icon) - Userlarni ochish
3. **Delete** (icon) - Verified userlarni o'chirish
4. **Delete unverified** (icon) - Unverified userlarni o'chirish

