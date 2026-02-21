<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Exercise;
use App\Models\Lesson;
use Illuminate\Database\Seeder;

class CoursesSeeder extends Seeder
{
    public function run(): void
    {
        $this->createCourse(
            'JavaScript od Zera -Zmienne i Typy',
            'Kurs dla absolutnie początkujących. Dowiesz się, jak komputer zapamiętuje dane (zmienne) i jak wypisuje tekst na ekranie.',
            500,
            20,
            'junior',
            'images/courses/js.png',
            [
                [
                    'title' => 'Teoria - co to jest zmienna?',
                    'content' => "Zmienna (let, const): To pudełko z nazwą, w którym trzymamy dane. Np. let imie = \"Adam\";.\n\nTekst (String): Napis w cudzysłowie, np. \"Kot\".\n\nLiczba (Number): Po prostu cyfry, np. 10 (bez cudzysłowu).\n\nconsole.log(): Polecenie, które \"wypluwa\" dane do terminala, żebyśmy mogli je zobaczyć.\n\nKod początkowy:\n```javascript\nlet powitanie = \"Cześć!\";\nconsole.log(powitanie);\n```",
                    'exercise' => null,
                ],
                [
                    'title' => 'Twoje pierwsze pudełko',
                    'content' => 'Stwórz zmienną o nazwie miasto i włóż do niej tekst "Kraków". Następnie wypisz ją używając console.log.',
                    'exercise' => [
                        'title' => 'Twoje pierwsze pudełko',
                        'description' => 'Stwórz zmienną o nazwie miasto i włóż do niej tekst "Kraków". Następnie wypisz ją używając console.log.',
                        'initial_code' => "// 1. Stwórz zmienną 'miasto'\n// 2. Wypisz ją",
                        'expected_output' => 'Kraków',
                        'judge0_language_id' => 63,
                        'hint' => 'Zmienną tworzymy słowem kluczowym let, a tekst podajemy w cudzysłowie. Do wypisania użyj console.log().',
                        'hint_2' => 'Wzorzec: let nazwaZmiennej = "wartość";',
                    ],
                ],
                [
                    'title' => 'Matematyka',
                    'content' => 'Komputer to świetny kalkulator. Stwórz zmienną wynik, która będzie sumą liczb 15 i 20. Wypisz wynik.',
                    'exercise' => [
                        'title' => 'Matematyka',
                        'description' => 'Komputer to świetny kalkulator. Stwórz zmienną wynik, która będzie sumą liczb 15 i 20. Wypisz wynik.',
                        'initial_code' => "// Stwórz zmienną wynik = 15 plus 20\nconsole.log(wynik);",
                        'expected_output' => '35',
                        'judge0_language_id' => 63,
                        'hint' => 'Aby zsumować dwie liczby, użyj operatora +. Wynik przypisz do zmiennej za pomocą let.',
                        'hint_2' => 'Wzorzec: let wynik = pierwsza + druga;',
                    ],
                ],
                [
                    'title' => 'Łączenie tekstu',
                    'content' => 'Możesz dodawać tekst tak samo jak liczby. Stwórz zmienną imie ("Jan") i zmienną nazwisko ("Kowalski"). Wypisz je połączone spacją.',
                    'exercise' => [
                        'title' => 'Łączenie tekstu',
                        'description' => 'Stwórz zmienną imie ("Jan") i zmienną nazwisko ("Kowalski"). Wypisz je połączone spacją.',
                        'initial_code' => "let imie = \"Jan\";\nlet nazwisko = \"Kowalski\";\n// Wypisz: imie + \" \" + nazwisko",
                        'expected_output' => 'Jan Kowalski',
                        'judge0_language_id' => 63,
                        'hint' => 'Do łączenia tekstów użyj operatora + — pamiętaj o spacji między nimi.',
                        'hint_2' => 'Możesz też użyć template literals: `${imie} ${nazwisko}`',
                    ],
                ],
            ]
        );

        $this->createCourse(
            'Bazy Danych – Wstęp do SQL',
            'Wyobraź sobie bazę danych jako wielki arkusz kalkulacyjny w Excelu. Nauczysz się z niego czytać.',
            400,
            15,
            'junior',
            'images/courses/sql.png',
            [
                [
                    'title' => 'Teoria – Tabela i SELECT',
                    'content' => "Tabela: Miejsce, gdzie są dane (np. tabela users).\n\nKolumna: Pionowy rząd danych (np. email, age).\n\nWiersz: Jeden wpis (jeden użytkownik).\n\nSELECT: \"Wybierz\".\n\nFROM: \"Z\".\n\nSkładnia: SELECT kolumna FROM tabela; (oznacza: Pokaż mi kolumnę z tej tabeli). Gwiazdka * oznacza \"wszystkie kolumny\".\n\nKod początkowy:\n```sql\n-- Przykład: Wybierz wszystko z tabeli users\nSELECT * FROM users;\n```",
                    'exercise' => null,
                ],
                [
                    'title' => 'Wybieranie konkretów',
                    'content' => 'Mamy tabelę products (produkty). Pobierz tylko kolumnę name (nazwa), aby zobaczyć listę nazw produktów bez ich cen.',
                    'exercise' => [
                        'title' => 'Wybieranie konkretów',
                        'description' => 'Pobierz tylko kolumnę name (nazwa) z tabeli products.',
                        'initial_code' => "-- Uzupełnij zapytanie\nSELECT ... FROM ...;",
                        'expected_output' => null,
                        'validation_regex' => '/SELECT\s+name\s+FROM\s+products/i',
                        'hint' => 'Składnia SELECT pozwala wybrać konkretne kolumny z tabeli. Wymień nazwy kolumn po przecinku, a na końcu wskaż tabelę po słowie FROM.',
                        'hint_2' => 'Wzorzec: SELECT kolumna FROM nazwa_tabeli;',
                    ],
                ],
                [
                    'title' => 'Warunek WHERE (Gdzie)',
                    'content' => 'WHERE pozwala filtrować. Np. WHERE id = 5. Wyświetl wszystkie dane produktu z tabeli products, który ma id równe 10.',
                    'exercise' => [
                        'title' => 'Warunek WHERE',
                        'description' => 'Wyświetl wszystkie dane produktu z tabeli products, który ma id równe 10.',
                        'initial_code' => 'SELECT * FROM products WHERE ...;',
                        'expected_output' => null,
                        'validation_regex' => '/SELECT\s+\*\s+FROM\s+products\s+WHERE\s+id\s*=\s*10/i',
                        'hint' => 'Klauzula WHERE filtruje wyniki. Po WHERE podaj warunek: nazwa_kolumny = wartość.',
                        'hint_2' => 'Porównanie wartości w SQL: kolumna = liczba (bez cudzysłowu dla liczb).',
                    ],
                ],
            ]
        );

        $this->createCourse(
            'PHP – Logika backendu',
            'Podstawy języka, który napędza większość Internetu. Zrozumienie zmiennych dolarowych i tablic.',
            450,
            25,
            'junior',
            'images/courses/php.png',
            [
                [
                    'title' => 'Teoria – Dolar i Tablice',
                    'content' => "$: W PHP każda zmienna musi zaczynać się od dolara, np. \$imie = \"Ala\";.\n\nTablica ([]): To lista rzeczy. Np. \$owoce = [\"Jablko\", \"Banan\"];.\n\nIndeks: Pozycja na liście. Komputery liczą od zera! \$owoce[0] to \"Jablko\".\n\nKod początkowy:\n```php\n<?php\n\$liczby = [10, 20, 30];\necho \$liczby[0]; // Wypisze 10\n```",
                    'exercise' => null,
                ],
                [
                    'title' => 'Twoja pierwsza tablica',
                    'content' => 'Stwórz tablicę $colors zawierającą trzy kolory: "red", "green", "blue".',
                    'exercise' => [
                        'title' => 'Twoja pierwsza tablica',
                        'description' => 'Stwórz tablicę $colors zawierającą trzy kolory: "red", "green", "blue".',
                        'initial_code' => "<?php\n// Zdefiniuj \$colors\nprint_r(\$colors);",
                        'expected_output' => "Array\n(\n    [0] => red\n    [1] => green\n    [2] => blue\n)",
                        'judge0_language_id' => 68, // PHP
                        'hint' => 'Tablicę tworzymy za pomocą nawiasów kwadratowych [], a elementy oddzielamy przecinkami.',
                        'hint_2' => 'Wzorzec: $zmienna = ["element1", "element2", "element3"];',
                    ],
                ],
                [
                    'title' => 'Pobieranie elementu',
                    'content' => 'Masz podaną tablicę $users. Wypisz (echo) drugiego użytkownika z listy (pamiętaj o liczeniu od zera!).',
                    'exercise' => [
                        'title' => 'Pobieranie elementu',
                        'description' => 'Wypisz (echo) drugiego użytkownika z listy $users.',
                        'initial_code' => "<?php\n\$users = [\"Admin\", \"Moderator\", \"User\"];\n// Wypisz \"Moderator\"",
                        'expected_output' => 'Moderator',
                        'judge0_language_id' => 68,
                        'hint' => 'Tablice w PHP są indeksowane od 0. Pierwszy element to indeks 0, drugi to 1, itd. Użyj echo i nawiasów kwadratowych.',
                        'hint_2' => 'Jeśli tablica to ["Admin","Moderator","User"], to jaki indeks ma "Moderator"?',
                    ],
                ],
                [
                    'title' => 'Liczenie elementów',
                    'content' => 'Funkcja count($tablica) zwraca liczbę elementów. Oblicz ile jest elementów w tablicy $data i zapisz wynik do zmiennej $ilosc.',
                    'exercise' => [
                        'title' => 'Liczenie elementów',
                        'description' => 'Oblicz ile jest elementów w tablicy $data i zapisz wynik do zmiennej $ilosc.',
                        'initial_code' => "<?php\n\$data = [1, 5, 2, 6, 8, 9];\n// Użyj count()\necho \$ilosc;",
                        'expected_output' => '6',
                        'judge0_language_id' => 68,
                        'hint' => 'PHP posiada wbudowaną funkcję, która zwraca liczbę elementów w tablicy. Jej nazwa to count().',
                        'hint_2' => 'Składnia: $zmienna = count($tablica);',
                    ],
                ],
            ]
        );

        $this->createCourse(
            'React – Komponenty funkcyjne',
            'Zrozumienie, jak buduje się interfejsy z klocków (komponentów) i czym są "propsy".',
            800,
            30,
            'mid',
            'images/courses/react.png',
            [
                [
                    'title' => 'Teoria – Komponent i Props',
                    'content' => "Komponent: To funkcja JavaScript, która zwraca kod HTML (JSX). Nazwa musi być z Wielkiej Litery.\n\nProps: To argumenty tej funkcji (jakby opcje konfiguracyjne klocka). Przekazujemy je tak: <Klocek kolor=\"czerwony\" />.\n\nDestrukturyzacja: Zamiast pisać props.kolor, piszemy w nawiasie funkcji ({ kolor }).\n\nKod początkowy:\n```javascript\n// Przykład:\nconst Powitanie = ({ imie }) => {\n    return <h1>Cześć, {imie}</h1>;\n};\n// Użycie: <Powitanie imie=\"Ewa\" />\n```",
                    'exercise' => null,
                ],
                [
                    'title' => 'Tworzenie komponentu',
                    'content' => 'Stwórz prosty komponent Alert, który zwraca <div>Uwaga!</div>.',
                    'exercise' => [
                        'title' => 'Tworzenie komponentu',
                        'description' => 'Stwórz prosty komponent Alert, który zwraca <div>Uwaga!</div>.',
                        'initial_code' => "import React from 'react';\n\n// Napisz funkcję Alert",
                        'expected_output' => null,
                        'validation_regex' => '/function\s+Alert|const\s+Alert\s*=\s*(function|\(.*\)\s*=>)/',
                        'hint' => 'Komponent w React to funkcja, która zwraca JSX (HTML-podobny kod). Stwórz funkcję strzałkową i użyj return z kodem JSX.',
                        'hint_2' => 'Wzorzec: const NazwaKomponentu = () => { return <znacznik>treść</znacznik>; };',
                    ],
                ],
                [
                    'title' => 'Użycie Props',
                    'content' => 'Zmodyfikuj komponent Alert, aby przyjmował props message i wyświetlał go wewnątrz diva.',
                    'exercise' => [
                        'title' => 'Użycie Props',
                        'description' => 'Zmodyfikuj komponent Alert, aby przyjmował props message i wyświetlał go wewnątrz diva.',
                        'initial_code' => "const Alert = (props) => {\n    // Zmień kod, aby użyć props.message\n    return <div>Stały tekst</div>;\n};",
                        'expected_output' => null,
                        'validation_regex' => '/\{(\s*props\.message|message)\s*\}/',
                        'hint' => 'Props to obiekt przekazywany do komponentu. Możesz go zdestrukturyzować w parametrze funkcji. Wewnątrz JSX użyj nawiasów klamrowych {} do wyświetlenia wartości.',
                        'hint_2' => 'Destrukturyzacja: const Komponent = ({ nazwaProp }) => ...',
                    ],
                ],
                [
                    'title' => 'Renderowanie warunkowe',
                    'content' => 'Komponent Status przyjmuje props isOnline (true/false). Jeśli true, zwróć "Dostępny", jeśli false "Niedostępny". Użyj operatora trójelementowego ? :.',
                    'exercise' => [
                        'title' => 'Renderowanie warunkowe',
                        'description' => 'Jeśli isOnline jest true, zwróć "Dostępny", jeśli false "Niedostępny".',
                        'initial_code' => "const Status = ({ isOnline }) => {\n    // return ...\n};",
                        'expected_output' => null,
                        'validation_regex' => '/isOnline\s*\?\s*[\'"]Dostępny[\'"]\s*:\s*[\'"]Niedostępny[\'"]/',
                        'hint' => 'Operator trójargumentowy (ternary) pozwala zwrócić różne wartości w zależności od warunku. Składnia: warunek ? wartośćTrue : wartośćFalse',
                        'hint_2' => 'Użyj nazwy propsa bezpośrednio jako warunku w operatorze ?.',
                    ],
                ],
            ]
        );

        $this->createCourse(
            'Laravel – Modele i Eloquent',
            'Jak kod PHP rozmawia z bazą danych bez pisania czystego SQL.',
            700,
            20,
            'mid',
            'images/courses/laravel.png',
            [
                [
                    'title' => 'Teoria – Model ORM',
                    'content' => "Model: To klasa w PHP, która reprezentuje tabelę w bazie. Np. model User reprezentuje tabelę users.\n\nObiektowość: Zamiast pisać SELECT * FROM users, piszemy User::all().\n\nSzukanie: User::find(1) to to samo co WHERE id = 1.\n\nStrzałka ->: Używamy jej do wyciągania właściwości obiektu, np. \$user->email.\n\nKod początkowy:\n```php\n// Przykład (tylko do odczytu)\n\$user = User::find(1);\necho \$user->name;\n```",
                    'exercise' => null,
                ],
                [
                    'title' => 'Pobranie rekordu',
                    'content' => 'Użyj modelu Product, aby znaleźć produkt o id 5 i przypisz go do zmiennej $product.',
                    'exercise' => [
                        'title' => 'Pobranie rekordu',
                        'description' => 'Znajdź produkt o id 5 i przypisz go do zmiennej $product.',
                        'initial_code' => "<?php\n// Znajdź produkt ID 5",
                        'expected_output' => null,
                        'validation_regex' => '/\$product\s*=\s*Product::find\(5\)/',
                        'hint' => 'Eloquent udostępnia statyczną metodę find() na modelach, która wyszukuje rekord po kluczu głównym.',
                        'hint_2' => 'Wzorzec: $zmienna = NazwaModelu::metoda(argument);',
                    ],
                ],
                [
                    'title' => 'Tworzenie nowego rekordu',
                    'content' => 'Stwórz nową instancję modelu Post, ustaw jego tytuł (title) na "Mój wpis" i zapisz go metodą save().',
                    'exercise' => [
                        'title' => 'Tworzenie nowego rekordu',
                        'description' => 'Stwórz nową instancję modelu Post, ustaw jego tytuł (title) na "Mój wpis" i zapisz go metodą save().',
                        'initial_code' => "<?php\n\$post = new Post();\n// Ustaw tytuł i zapisz",
                        'expected_output' => null,
                        'validation_regex' => '/(?=.*\$post->title\s*=\s*[\'"]Mój wpis[\'"])(?=.*\$post->save\(\))/s',
                        'hint' => 'Aby ustawić wartość pola modelu, użyj operatora strzałki: $obiekt->pole = wartość. Następnie wywołaj metodę save().',
                        'hint_2' => 'Po przypisaniu wartości do pola, wywołaj $obiekt->save() bez argumentów.',
                    ],
                ],
            ]
        );

        $this->createCourse(
            'Node.js – Asynchroniczność i Event Loop',
            'Jak JavaScript wykonuje zadania "w tle" nie blokując reszty aplikacji. Kluczowe dla wydajności.',
            1500,
            35,
            'senior',
            'images/courses/nodejs.png',
            [
                [
                    'title' => 'Teoria – Sync vs Async i Promise',
                    'content' => "Blokowanie (Sync): Kod czeka, aż zadanie się skończy (np. czytanie dużego pliku). To źle, bo strona \"wisi\".\n\nNieblokowanie (Async): Kod zleca zadanie i idzie dalej. Gdy zadanie się skończy, wywoływana jest funkcja zwrotna (callback).\n\nPromise (Obietnica): Obiekt, który mówi \"Zaraz dam ci wynik albo błąd\". Ma metody .then() (sukces) i .catch() (błąd).\n\nAsync/Await: Nowoczesny sposób zapisu Promisów, który wygląda jak kod synchroniczny.\n\nKod początkowy:\n```javascript\n// Przykład async/await\nasync function getData() {\n   const data = await database.query(); // Czekaj tutaj, ale nie blokuj innych\n   console.log(data);\n}\n```",
                    'exercise' => null,
                ],
                [
                    'title' => 'Użycie Promise',
                    'content' => 'Funkcja wait(ms) zwraca Promise. Użyj .then(), aby wypisać "Gotowe" po jej zakończeniu.',
                    'exercise' => [
                        'title' => 'Użycie Promise',
                        'description' => 'Funkcja wait(ms) zwraca Promise. Użyj .then(), aby wypisać "Gotowe" po jej zakończeniu.',
                        'initial_code' => "wait(1000)\n  // Dopisz .then",
                        'expected_output' => null,
                        'validation_regex' => '/\.then\s*\(\s*\(\s*\)\s*=>\s*console\.log\s*\(\s*[\'"]Gotowe[\'"]\s*\)\s*\)/',
                        'hint' => 'Metoda .then() pozwala wywołać funkcję po zakończeniu Promise. Przekaż do niej funkcję strzałkową, która wykona console.log().',
                        'hint_2' => 'Wzorzec: promise.then(() => { /* co zrobić po zakończeniu */ });',
                    ],
                ],
                [
                    'title' => 'Konwersja na Async/Await',
                    'content' => 'Przerób funkcję login, która używa .then(), na nowoczesną składnię async/await.',
                    'exercise' => [
                        'title' => 'Konwersja na Async/Await',
                        'description' => 'Przerób funkcję login, która używa .then(), na nowoczesną składnię async/await.',
                        'initial_code' => "function login(user) {\n   return checkUser(user).then(isValid => {\n       return isValid ? \"OK\" : \"Error\";\n   });\n}",
                        'expected_output' => null,
                        'validation_regex' => '/async\s+function\s+login.*await\s+checkUser/',
                        'hint' => 'Słowo kluczowe async przed function oznacza funkcję asynchroniczną. Wewnątrz niej możesz użyć await zamiast .then().',
                        'hint_2' => 'Zamień .then(callback) na: const wynik = await wywołanie;',
                    ],
                ],
                [
                    'title' => 'Obsługa błędów (Try/Catch)',
                    'content' => 'Wewnątrz funkcji async wywołaj riskyOperation(). Jeśli wystąpi błąd, złap go (catch) i zwróć string "Awaria".',
                    'exercise' => [
                        'title' => 'Obsługa błędów',
                        'description' => 'Wewnątrz funkcji async wywołaj riskyOperation(). Jeśli wystąpi błąd, złap go (catch) i zwróć string "Awaria".',
                        'initial_code' => "async function doWork() {\n   // Użyj try...catch\n   const result = await riskyOperation();\n   return result;\n}",
                        'expected_output' => null,
                        'validation_regex' => '/try\s*{.*catch\s*\(e\)\s*{\s*return\s*[\'"]Awaria[\'"]/',
                        'hint' => 'Blok try { } otacza kod, który może rzucić błąd. Blok catch(e) { } łapie ten błąd i pozwala na obsługę.',
                        'hint_2' => 'Wewnątrz catch zwróć odpowiedni tekst za pomocą return.',
                    ],
                ],
            ]
        );

        $this->createCourse(
            'Zaawansowany SQL – Grupowanie i Agregacja',
            'Generowanie raportów i statystyk bezpośrednio w bazie danych.',
            1300,
            25,
            'senior',
            'images/courses/advanced_sql.png',
            [
                [
                    'title' => 'Teoria – Agregacja (SUM, COUNT, GROUP BY)',
                    'content' => "Funkcje agregujące: Biorą wiele wierszy i zwracają jedną liczbę.\n\nCOUNT(*): Policz ile wierszy.\n\nSUM(kolumna): Dodaj wartości.\n\nAVG(kolumna): Średnia.\n\nGROUP BY: \"Dla każdego...\". Np. \"Policz zamówienia DLA KAŻDEGO użytkownika\". Grupuje wyniki według podanej kolumny.\n\nHAVING: To samo co WHERE, ale działa na wynikach grupowania (np. \"tylko ci, co mają więcej niż 5 zamówień\").\n\nKod początkowy:\n```sql\n-- Policz średnią cenę produktów w każdej kategorii\nSELECT category, AVG(price) FROM products GROUP BY category;\n```",
                    'exercise' => null,
                ],
                [
                    'title' => 'Suma sprzedaży',
                    'content' => 'Masz tabelę sales (id, amount, date). Oblicz łączną sumę (SUM) pola amount dla wszystkich sprzedaży.',
                    'exercise' => [
                        'title' => 'Suma sprzedaży',
                        'description' => 'Oblicz łączną sumę (SUM) pola amount dla wszystkich sprzedaży.',
                        'initial_code' => 'SELECT ...',
                        'expected_output' => null,
                        'validation_regex' => '/SELECT\s+SUM\(amount\)\s+FROM\s+sales/i',
                        'hint' => 'Funkcje agregujące w SQL wykonują obliczenia na zestawie wartości. SUM() sumuje wszystkie wartości w kolumnie.',
                        'hint_2' => 'Wzorzec: SELECT FUNKCJA(kolumna) FROM tabela;',
                    ],
                ],
                [
                    'title' => 'Najlepsi klienci',
                    'content' => 'Wyświetl user_id oraz sumę wydanych pieniędzy (amount) dla każdego użytkownika. Posortuj malejąco wg wydanej kwoty.',
                    'exercise' => [
                        'title' => 'Najlepsi klienci',
                        'description' => 'Wyświetl user_id oraz sumę wydanych pieniędzy (amount) dla każdego użytkownika. Posortuj malejąco wg wydanej kwoty.',
                        'initial_code' => "SELECT user_id, SUM(amount) as total\nFROM sales\n-- Dodaj grupowanie i sortowanie",
                        'expected_output' => null,
                        'validation_regex' => '/GROUP\s+BY\s+user_id\s+ORDER\s+BY\s+total\s+DESC/i',
                        'hint' => 'GROUP BY grupuje wyniki według kolumny. ORDER BY sortuje wyniki — dodaj DESC dla malejąco.',
                        'hint_2' => 'Dodaj klauzule po FROM sales w odpowiedniej kolejności: najpierw grupowanie, potem sortowanie.',
                    ],
                ],
            ]
        );

        $this->createCourse(
            'Wprowadzenie do ReactJS',
            'Kompletny kurs od podstaw dla początkujących. Nauczysz się komponentów, hooków i stanu.',
            600,
            30,
            'junior',
            'images/courses/react.png',
            [
                [
                    'title' => 'Lekcja 0: Wstęp do JSX',
                    'content' => 'JSX to składnia pozwalająca pisać HTML w JavaScript. Każdy tag musi być zamknięty.',
                    'exercise' => [
                        'title' => 'Twój pierwszy tag',
                        'description' => 'Napisz funkcję zwracającą prosty przycisk z napisem "Start".',
                        'initial_code' => "function Button() {\n  // Zwróć kod HTML (JSX) dla przycisku\n  return \"<button>...</button>\";\n}",
                        'test_code' => "try {\n  const result = Button();\n  console.log(result);\n} catch(e) { console.error(e.message); }",
                        'expected_output' => '<button>Start</button>',
                        'hint' => 'W React funkcja komponentu zwraca JSX — składnię przypominającą HTML. Zamiast tekstu w cudzysłowie, zwróć element JSX bezpośrednio.',
                        'hint_2' => 'Zamień return "tekst" na return <element>tekst</element> — bez cudzysłowu.',
                    ],
                ],
                [
                    'title' => 'Lekcja 1: Co to jest React?',
                    'content' => 'React to biblioteka JavaScript służąca do budowania interfejsów użytkownika...',
                    'exercise' => [
                        'title' => 'Twój pierwszy komponent',
                        'description' => 'Stwórz prosty komponent funkcyjny, który zwraca div z tekstem "Hello World".',
                        'initial_code' => "function HelloWorld() {\n  // Zmień poniższą linię, aby zwracała <div>Hello World</div>\n  return \"<div>...</div>\";\n}",
                        'test_code' => 'try { console.log(HelloWorld()); } catch(e) { console.error(e.message); }',
                        'expected_output' => '<div>Hello World</div>',
                        'hint' => 'Komponent funkcyjny to zwykła funkcja JavaScript, która zwraca element JSX. JSX wygląda jak HTML, ale jest wbudowany bezpośrednio w JavaScript.',
                        'hint_2' => 'Zwróć element <div> z odpowiednim tekstem wewnątrz — bez cudzysłowów wokół JSX.',
                    ],
                ],
                [
                    'title' => 'Lekcja 2: Stan (State) i Props',
                    'content' => 'W tej lekcji dowiesz się, jak przekazywać dane między komponentami...',
                    'exercise' => [
                        'title' => 'Użycie useState',
                        'description' => 'Stwórz licznik, który zwiększa się po kliknięciu przycisku. Użyj hooka useState z wartością początkową 0.',
                        'initial_code' => "import React, { useState } from 'react';\n\nfunction Counter() {\n  // Stwórz stan 'count' z wartością początkową 0\n  // const [count, setCount] = useState(...);\n\n  return (\n    <div>\n      <p>Licznik: {/* wyświetl count */}</p>\n      <button onClick={() => { /* zwiększ count o 1 */ }}>\n        Kliknij\n      </button>\n    </div>\n  );\n}",
                        'validation_regex' => '/useState\s*\(\s*0\s*\)/',
                        'hint' => 'Hook useState() przyjmuje wartość początkową i zwraca tablicę: [aktualnaWartość, funkcjaZmieniająca]. Użyj destrukturyzacji.',
                        'hint_2' => 'Do zwiększania wartości użyj setCount(count + 1) lub setCount(prev => prev + 1) w onClick.',
                    ],
                ],
            ]
        );
    }

    private function createCourse(string $title, string $description, int $reward, int $duration, string $level, string $imagePath, array $lessonsData): void
    {
        $course = Course::create([
            'title' => $title,
            'description' => $description,
            'reward' => $reward,
            'duration' => $duration,
            'level' => $level,
            'image_path' => $imagePath,
        ]);

        foreach ($lessonsData as $index => $data) {
            $lesson = Lesson::create([
                'course_id' => $course->id,
                'title' => $data['title'],
                'content' => $data['content'],
                'order' => $index, // 0-based index matches our requirement for Lesson 0
            ]);

            if (isset($data['exercise'])) {
                Exercise::create([
                    'lesson_id' => $lesson->id,
                    'title' => $data['exercise']['title'],
                    'description' => $data['exercise']['description'],
                    'initial_code' => $data['exercise']['initial_code'],
                    'expected_output' => $data['exercise']['expected_output'] ?? null,
                    'validation_regex' => $data['exercise']['validation_regex'] ?? null,
                    'judge0_language_id' => $data['exercise']['judge0_language_id'] ?? null,
                    'hint' => $data['exercise']['hint'] ?? '',
                    'hint_2' => $data['exercise']['hint_2'] ?? null,
                    'test_code' => $data['exercise']['test_code'] ?? null,
                ]);
            }
        }
    }
}
