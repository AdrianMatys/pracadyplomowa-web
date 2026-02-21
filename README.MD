# LearnProgramming

Aplikacja webowa do nauki programowania oparta na Laravel i Vue.js.

## Wymagania

- PHP 8.2+
- Composer
- Node.js & NPM
- PostgreSQL (lub SQLite)

## Instalacja

1. **Klonowanie repozytorium**

   ```bash
   git clone https://github.com/AdrianMatys/PracaDyplomowa-web.git
   cd PracaDyplomowa-web
   ```

2. **Instalacja zależności PHP (Backend)**

   ```bash
   composer install
   ```

3. **Instalacja zależności JavaScript (Frontend)**

   ```bash
   npm install
   npm run build
   ```

4. **Konfiguracja środowiska**
   Skopiuj przykładowy plik konfiguracyjny `.env` i wygeneruj klucz aplikacji:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Baza danych i pliki**
   Uruchom migracje, aby utworzyć tabele, i wypełnij je danymi testowymi:

   ```bash
   php artisan migrate --seed
   ```

   Utwórz dowiązanie symboliczne do plików publicznych:

   ```bash
   php artisan storage:link
   ```

6. **Uruchomienie**
   ```bash
   php artisan serve
   ```

## Dostęp

Aplikacja będzie dostępna pod adresem: http://localhost:8000

---

### Dane logowania (Administrator)

- **Email**: `admin@learnprogramming.com`
- **Hasło**: `password`
