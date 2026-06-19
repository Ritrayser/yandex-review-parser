markdown
# Yandex Reviews Parser

Приложение для сбора отзывов с Яндекс.Карт.  
**Стек:** Laravel 11 + Vue 3 + Docker Sail.

---

### 1. Бэкенд

```bash
cp .env.example .env
./vendor/bin/sail composer install
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate --seed
Бэкенд доступен по адресу: http://localhost:8080

 2. Фронтенд

cd frontend
npm install
npm run dev
Фронтенд доступен по адресу: http://localhost:5173 (или 5174, если порт занят)

