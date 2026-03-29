# 📅 System Rezerwacji Zasobów

Prosty, ale solidny system do zarządzania rezerwacjami (np. sal lekcyjnych, sprzętu IT czy biurek), stworzony w ramach nauki programowania (Technik Programista, klasa 2). Projekt demonstruje pełną ścieżkę przepływu danych: od formularza użytkownika, przez walidację w PHP, aż po zapis w bazie MySQL.

## 🚀 Główne Funkcje
- **Pełny CRUD**: Możliwość dodawania, przeglądania i usuwania rezerwacji w czasie rzeczywistym.
- **Inteligentna Walidacja Czasu**:
  - Blokada rezerwacji terminów, które już minęły (data i godzina).
  - **System anty-konfiktowy**: Aplikacja automatycznie sprawdza, czy wybrany zasób nie jest już zajęty i wymaga minimum 1-godzinnego odstępu między rezerwacjami.
- **Bezpieczeństwo**: 
  - Ochrona przed **SQL Injection** dzięki zastosowaniu `Prepared Statements` (PDO).
  - Ochrona przed **XSS** poprzez filtrowanie danych wyjściowych (`htmlspecialchars`).
- **Modern UI**: Wyśrodkowany, responsywny interfejs przygotowany w czystym CSS.

## 🛠️ Stack Technologiczny
- **Język:** PHP 8.x
- **Baza danych:** MySQL / MariaDB
- **Frontend:** HTML5, CSS3 (Flexbox)

## 📦 Struktura Projektu
- `index.php` - główna logika aplikacji i interfejs.
- `db.php` - konfiguracja połączenia z bazą danych (PDO).
- `database.sql` - gotowy schemat bazy danych do zaimportowania.
- `README.md` - dokumentacja projektu.

## ⚙️ Jak uruchomić projekt lokalnie?
1. Sklonuj repozytorium do swojego folderu serwera (np. `htdocs` w XAMPP).
2. Zaimportuj plik `database.sql` w środowisku **phpMyAdmin**.
3. W pliku `db.php` ustaw poprawne dane do swojej bazy danych (host, nazwa bazy, login, hasło).
4. Otwórz przeglądarkę i wpisz `localhost/nazwa_folderu`.

---
*Projekt rozwijany w celach edukacyjnych jako element portfolio na praktyki zawodowe.*
