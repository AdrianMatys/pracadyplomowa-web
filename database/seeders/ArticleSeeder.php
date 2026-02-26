<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        \DB::statement('SET session_replication_role = replica;');
        Article::query()->delete();
        \DB::statement('SET session_replication_role = DEFAULT;');

        if (Tag::count() === 0) {
            $this->call(TagSeeder::class);
        }

        $userId = \App\Models\User::where('role', 'admin')->first()->id ?? 1;

        $articles = [
            [
                'title' => 'SOLID - pięć zasad dobrego projektowania obiektowego',
                'type' => 'article',
                'estimated_time' => 8,
                'tags' => ['Backend', 'PHP'],
                'content' => <<<'MD'
Zasady SOLID to fundament dobrego projektowania oprogramowania. Akronim ten pochodzi od pierwszych liter pięciu zasad:

**S - Single Responsibility Principle (Zasada jednej odpowiedzialności)**
Każda klasa powinna mieć jeden i tylko jeden powód do zmiany. Oznacza to, że klasa powinna skupiać się wyłącznie na jednym zadaniu, co ułatwia testowanie i utrzymanie kodu.

**O - Open/Closed Principle (Zasada otwarte/zamknięte)**
Moduły powinny być otwarte na rozszerzenie, ale zamknięte na modyfikację. Nowe funkcjonalności dodajemy przez rozszerzanie klasy (dziedziczenie, kompozycja), a nie przez zmianę istniejącego kodu.

**L - Liskov Substitution Principle (Zasada podstawienia Liskov)**
Obiekty klasy bazowej powinny być zastępowalne przez obiekty klas pochodnych bez wpływu na poprawność działania programu. Klasy dziedziczące nie mogą zawężać kontraktu klasy nadrzędnej.

**I - Interface Segregation Principle (Zasada segregacji interfejsów)**
Klient nie powinien być zmuszany do implementowania metod, których nie potrzebuje. Zamiast jednego dużego interfejsu, tworzymy wiele małych, wyspecjalizowanych.

**D - Dependency Inversion Principle (Zasada odwrócenia zależności)**
Moduły wysokiego poziomu nie powinny zależeć od modułów niskiego poziomu - obydwa powinny zależeć od abstrakcji. Dzięki temu łatwo podmieniamy implementacje (np. przy testach).

Stosowanie SOLID sprawia, że kod jest bardziej elastyczny, czytelny i łatwiejszy do testowania.
MD,
            ],
            [
                'title' => 'DRY, KISS i YAGNI - zasady, które upraszczają kod',
                'type' => 'article',
                'estimated_time' => 5,
                'tags' => ['Frontend', 'Backend'],
                'content' => <<<'MD'
Trzy proste zasady, które każdy developer powinien znać na pamięć:

**DRY - Don't Repeat Yourself (Nie powtarzaj się)**
Każda wiedza w systemie powinna mieć jedno, jednoznaczne, autorytatywarne odwzorowanie. Powtarzanie kodu prowadzi do niespójności - gdy zmienia się logika w jednym miejscu, trzeba pamiętać o aktualizacji we wszystkich kopiach. Wyciągaj powtarzający się kod do funkcji, klas lub modułów.

**KISS - Keep It Simple, Stupid (Utrzymuj prostotę)**
Prostota jest kluczem. Unikaj nadmiernej abstrakcji i skomplikowanych rozwiązań tam, gdzie proste wystarczą. Kod prosty jest łatwiejszy do zrozumienia, testowania i debugowania. Nie komplikuj czegoś, co może być proste.

**YAGNI - You Aren't Gonna Need It (Nie będziesz tego potrzebować)**
Nie implementuj funkcjonalności z góry, jeśli nie jest ona aktualnie wymagana. Programiści często piszą kod "na przyszłość", który nigdy nie jest używany, a jednocześnie zwiększa złożoność systemu. Buduj to, czego potrzebujesz teraz.

Stosowanie tych trzech zasad w połączeniu z SOLID prowadzi do kodu, który jest czytelny, łatwy w utrzymaniu i skalowalny.
MD,
            ],
            [
                'title' => 'Wzorce projektowe - przegląd najważniejszych',
                'type' => 'article',
                'estimated_time' => 10,
                'tags' => ['Backend', 'PHP', 'JavaScript'],
                'content' => <<<'MD'
Wzorce projektowe to sprawdzone rozwiązania powtarzających się problemów w projektowaniu oprogramowania. Dzielimy je na trzy kategorie:

## Wzorce kreacyjne
Odpowiadają za mechanizmy tworzenia obiektów.
- **Singleton** - gwarantuje istnienie tylko jednej instancji klasy w całej aplikacji.
- **Factory Method** - definiuje interfejs tworzenia obiektów, pozostawiając decyzję o konkretnej klasie podklasom.
- **Builder** - pozwala na krok po kroku budowanie skomplikowanych obiektów.

## Wzorce strukturalne
Opisują sposoby łączenia obiektów w większe struktury.
- **Adapter** - pozwala obiektom o niekompatybilnych interfejsach współpracować ze sobą.
- **Decorator** - dynamicznie dodaje nowe obowiązki do obiektu.
- **Facade** - dostarcza uproszczony interfejs do złożonego podsystemu.

## Wzorce behawioralne
Dotyczą interakcji między obiektami.
- **Observer** - definiuje zależność jeden-do-wielu. Gdy jeden obiekt zmienia stan, wszystkie zależne obiekty są automatycznie powiadamiane.
- **Strategy** - definiuje rodzinę algorytmów, hermetyzuje każdy z nich i pozwala na ich wymienność.
- **Command** - enkapsuluje żądanie jako obiekt, umożliwiając kolejkowanie i cofanie operacji.

Znajomość wzorców projektowych pozwala szybciej komunikować się w zespole i rozwiązywać typowe problemy architektoniczne.
MD,
            ],
            [
                'title' => 'Jak pisać czysty kod - praktyczny przewodnik',
                'type' => 'article',
                'estimated_time' => 7,
                'tags' => ['Frontend', 'Backend', 'JavaScript'],
                'content' => <<<'MD'
Czysty kod to kod, który jest łatwy do czytania i rozumienia przez innych developerów. Oto kluczowe zasady:

## Znaczące nazwy
Używaj nazw, które jasno opisują cel zmiennej, funkcji lub klasy. Zamiast `$d` (liczba dni) - używaj `$elapsedDays`. Unikaj skrótów, które nie są powszechnie znane.

## Małe funkcje
Funkcja powinna robić tylko jedną rzecz i robić to dobrze. Jeśli funkcja liczy więcej niż 20 linii, prawdopodobnie robi za dużo. Wyciągaj logikę do pomocniczych funkcji o opisowych nazwach.

## Unikaj komentarzy jako substytutu dobrego kodu
Komentarz "// liczymy wiek użytkownika" powyżej `$a = $b - $c;` to sygnał do przepisania kodu. Dobry kod jest samodokumentujący. Komentarze są wartościowe, gdy opisują *dlaczego*, a nie *co*.

## Obsługa błędów
Nie ukrywaj błędów w pustych blokach catch. Każdy błąd powinien być obsłużony lub przelogowany. Preferuj wyjątki nad kody błędów.

## Formatowanie
Spójne formatowanie kodu ułatwia czytanie. Używaj linterów i formatterów (ESLint, PHP CS Fixer), by wymuszać jednolity styl w całym projekcie.

## Testy
Kod bez testów jest kodem trudnym do zmiany. Pisz unit testy dla logiki biznesowej - to najlepsza dokumentacja zachowania kodu.
MD,
            ],
            [
                'title' => 'Nazewnictwo w kodzie - jak się nie pogubić',
                'type' => 'article',
                'estimated_time' => 4,
                'tags' => ['Frontend', 'Backend'],
                'content' => <<<'MD'
Dobre nazewnictwo to jedna z najtrudniejszych umiejętności w programowaniu. Zrozumiałe nazwy zmiennych, funkcji i klas drastycznie redukują czas potrzebny na zrozumienie kodu.

## Zmienne
- Używaj **rzeczowników** lub **frazy rzeczownikowej**: `$userAge`, `$orderItems`.
- Booleans powinny brzmieć jak pytania: `$isLoggedIn`, `$hasPermission`, `$canEdit`.
- Unikaj skrótów: `$usr` → `$user`, `$tmp` → `$temporaryValue`.

## Funkcje i metody
- Używaj **czasowników**: `getUserById()`, `calculateTotal()`, `sendEmail()`.
- Nie bój się długich nazw - `getUsersWithActiveSubscription()` jest lepsze niż `getUsers()`.

## Klasy
- Używaj **rzeczowników w liczbie pojedynczej**: `UserRepository`, `OrderService`, `PaymentGateway`.

## Stałe
- Używaj `UPPER_SNAKE_CASE`: `MAX_RETRY_COUNT`, `DEFAULT_TIMEOUT_MS`.

## Konwencje w projekcie
Najważniejsza jest **spójność**. Jeśli cały projekt używa `camelCase`, nie używaj `snake_case` w swoich plikach. Trzymaj się konwencji języka i frameworka - PHPstanowi `snake_case` dla metod, JavaScript preferuje `camelCase`.
MD,
            ],

            // --- CATALOGING / ORGANIZATION ---
            [
                'title' => 'Dobre praktyki organizacji struktury projektu',
                'type' => 'article',
                'estimated_time' => 6,
                'tags' => ['Backend', 'Laravel', 'Frontend'],
                'content' => <<<'MD'
Dobrze zorganizowana struktura katalogów projektu to podstawa pracy zespołowej i łatwego onboardingu nowych developerów.

## Zasady ogólne
- **Grupuj po funkcjonalności, nie po typie pliku.** Zamiast `js/components/`, `js/pages/`, `js/stores/` - rozważ `features/auth/`, `features/courses/`, gdzie każda funkcjonalność ma swoje komponenty, store i composables.
- **Zachowaj płaską strukturę tam, gdzie to możliwe.** Głęboka hierarchia katalogów utrudnia nawigację. Maksymalnie 3-4 poziomy zagłębienia.

## Struktura backendu (Laravel)
Trzymaj się konwencji Laravel: `app/Models`, `app/Http/Controllers`, `app/Services`. Jeśli projekt rośnie, rozważ **Domain-Driven Design** (DDD) i grupowanie po domenach: `app/Domain/User/`, `app/Domain/Course/`.

## Struktura frontendu (Vue/React)
```
resources/js/
├── components/     # Wielokrotnego użytku, bezstanowe
├── composables/    # Logika wielokrotnego użytku (hooks)
├── pages/          # Widoki powiązane z routami
├── stores/         # Stan aplikacji (Pinia/Vuex)
├── services/       # Warstwa komunikacji z API
└── types/          # Typy TypeScript
```

## Nazewnictwo plików
- Komponenty Vue/React: `PascalCase` - `UserCard.vue`, `CourseList.vue`.
- Composables: prefix `use` - `useAuth.ts`, `useCourseData.ts`.
- Testy: sufiks `.test` lub `.spec` - `UserCard.spec.ts`.

Dobrze zorganizowany projekt pozwala nowemu developerowi w ciągu kilku minut zrozumieć, gdzie szukać konkretnego kodu.
MD,
            ],
            [
                'title' => 'Git - dobre praktyki pracy z repozytorium',
                'type' => 'article',
                'estimated_time' => 7,
                'tags' => ['Backend', 'Frontend'],
                'content' => <<<'MD'
Git to narzędzie, które przy złej higienie pracy może stać się źródłem chaosu. Oto zasady, które sprawią, że historia repozytorium będzie czytelna i użyteczna.

## Conventional Commits
Używaj konwencji Conventional Commits do nazywania commitów:
- `feat:` - nowa funkcjonalność
- `fix:` - naprawa błędu
- `docs:` - zmiany w dokumentacji
- `refactor:` - refaktoryzacja bez zmiany zachowania
- `test:` - dodanie lub poprawa testów
- `chore:` - rutynowe zadania (aktualizacja zależności itp.)

Przykład: `feat(auth): add JWT refresh token support`

## Małe, atomowe commity
Każdy commit powinien reprezentować jedną, logiczną zmianę. Duże commity z setkami zmian są trudne do review i do cofnięcia. Reguła: jeśli nie możesz opisać commita jednym zdaniem, podziel go.

## Praca z gałęziami
- `main` / `master` - zawsze deployowalny kod produkcyjny.
- `develop` - gałąź integracyjna (jeśli stosujesz GitFlow).
- Funkcjonalności: `feature/user-authentication`, `feature/course-progress`.
- Naprawy: `fix/login-redirect-loop`, `hotfix/payment-calculation`.

## Code Review
Traktuj PR (Pull Request) jako narzędzie do nauki, nie do oceniania. Komentarze powinny być konstruktywne i skupione na kodzie, nie na osobie. Review powinno sprawdzać: poprawność logiki, bezpieczeństwo, czytelność, testy.

## .gitignore
Nigdy nie commituj plików konfiguracyjnych ze środowiska (`.env`), katalogów z zależnościami (`node_modules/`, `vendor/`) ani artefaktów budowania.
MD,
            ],

            // --- DATABASES ---
            [
                'title' => 'Normalizacja baz danych - od 1NF do 3NF',
                'type' => 'article',
                'estimated_time' => 9,
                'tags' => ['Databases', 'SQL', 'Backend'],
                'content' => <<<'MD'
Normalizacja to proces organizowania danych w bazie danych w celu zmniejszenia redundancji i poprawy integralności danych.

## Pierwsza postać normalna (1NF)
Tabela jest w 1NF, jeśli:
- Każda kolumna zawiera wartości atomowe (niepodzielne).
- Każda kolumna zawiera wartości tego samego typu.
- Każdy wiersz jest unikalny (ma klucz główny).

*Przykład naruszenia:* kolumna `tagi` zawierająca `"PHP, Laravel, Backend"` - to nie jest atomowa wartość. Rozwiązanie: tabela `tagi` z relacją wiele-do-wielu.

## Druga postać normalna (2NF)
Tabela jest w 2NF, jeśli jest w 1NF i każda kolumna niekluczowa jest w pełni funkcjonalnie zależna od klucza głównego (dotyczy tylko tabel z kluczem złożonym).

*Przykład naruszenia:* tabela `zamowienia_produkty(zamowienie_id, produkt_id, cena_produktu)` - cena produktu zależy od `produkt_id`, nie od całego klucza złożonego. Przenieś cenę do tabeli `produkty`.

## Trzecia postać normalna (3NF)
Tabela jest w 3NF, jeśli jest w 2NF i żadna kolumna niekluczowa nie zależy tranzytywnie od klucza głównego.

*Przykład naruszenia:* tabela `pracownicy(id, dzial_id, nazwa_dzialu)` - `nazwa_dzialu` zależy od `dzial_id`, a nie bezpośrednio od `id`. Rozwiązanie: oddzielna tabela `dzialy`.

## Kiedy denormalizować?
W systemach OLAP (hurtowniach danych) i przy optymalizacji wydajności, celowo denormalizujemy dane, by unikać kosztownych JOIN-ów. W typowych aplikacjach webowych dążymy do 3NF.
MD,
            ],
            [
                'title' => 'Indeksy w SQL - kiedy i jak ich używać',
                'type' => 'article',
                'estimated_time' => 6,
                'tags' => ['Databases', 'SQL'],
                'content' => <<<'MD'
Indeksy to jedna z najpotężniejszych technik optymalizacji zapytań SQL, ale używane niewłaściwie mogą pogorszyć wydajność.

## Czym jest indeks?
Indeks to dodatkowa struktura danych (zwykle B-drzewo), która przyspiesza wyszukiwanie rekordów. Działa jak indeks w książce - zamiast przeglądać każdą stronę, trafiasz bezpośrednio do właściwej.

## Kiedy tworzyć indeksy?
- Na kolumnach używanych w klauzulach `WHERE`, `JOIN`, `ORDER BY` i `GROUP BY`.
- Na kluczach obcych (foreign keys) - bazy danych często nie tworzą ich automatycznie.
- Na kolumnach z wysoką selektywnością (wiele unikalnych wartości).

## Kiedy **nie** tworzyć indeksów?
- Na małych tabelach (kilkaset wierszy) - full scan jest szybszy.
- Na kolumnach z niską selektywnością (np. kolumna `is_active` z wartościami 0/1).
- Na tabelach z intensywnym zapisem - każdy `INSERT`/`UPDATE`/`DELETE` musi aktualizować indeksy.

## Typy indeksów
- **Unikalny (UNIQUE)** - gwarantuje unikalność wartości w kolumnie.
- **Złożony (Composite)** - indeks na wielu kolumnach. Kolejność kolumn ma znaczenie.
- **Częściowy (Partial)** - indeksuje tylko wiersze spełniające warunek (PostgreSQL).
- **Pełnotekstowy (Full-text)** - do wyszukiwania w treści tekstowej.

## Narzędzia diagnostyczne
Używaj `EXPLAIN ANALYZE` w PostgreSQL lub `EXPLAIN` w MySQL, by zobaczyć, jak baza realizuje zapytanie i czy korzysta z indeksów.
MD,
            ],

            // --- FRONTEND ---
            [
                'title' => 'Dostępność (a11y) w aplikacjach webowych - od czego zacząć',
                'type' => 'article',
                'estimated_time' => 5,
                'tags' => ['Frontend', 'JavaScript'],
                'content' => <<<'MD'
Dostępność (accessibility, a11y) to projektowanie aplikacji w sposób umożliwiający korzystanie z nich osobom z różnymi niepełnosprawnościami. To nie tylko etyczny obowiązek, ale w wielu krajach - wymóg prawny.

## Semantyczny HTML
Używaj właściwych elementów HTML do właściwych celów. `<button>` dla przycisków, `<a>` dla linków, `<nav>` dla nawigacji, `<main>` dla głównej treści. Czytniki ekranu (screen readery) opierają się na semantyce HTML.

## Alternatywne teksty dla obrazów
Każdy obraz znaczący treściowo powinien mieć atrybut `alt` z opisem. Obrazy dekoracyjne powinny mieć `alt=""` (pustą wartość), by screen reader je pomijał.

## Kontrast kolorów
WCAG 2.1 wymaga stosunku kontrastu co najmniej **4.5:1** dla tekstu normalnego i **3:1** dla tekstu dużego. Narzędzia jak Contrast Checker lub wbudowane DevTools pomagają to weryfikować.

## Fokus klawiatury
Każdy interaktywny element (łącza, przyciski, pola formularza) musi być dostosiany za pomocą klawiatury. Nie używaj `outline: none` bez dostarczenia alternatywnego stylu focusa.

## ARIA
Atrybuty ARIA (`role`, `aria-label`, `aria-expanded`, etc.) uzupełniają semantykę tam, gdzie sam HTML nie wystarcza (np. niestandardowe komponenty dropdown). Używaj ARIA dopiero gdy natywny HTML nie wystarczy - "No ARIA is better than bad ARIA".

## Testowanie
Użyj narzędzi jak Axe DevTools, Lighthouse czy Wave do automatycznego audytu dostępności. Ale nic nie zastąpi testowania z prawdziwym czytnikiem ekranu (NVDA, VoiceOver).
MD,
            ],
            [
                'title' => 'Optymalizacja wydajności frontendu - praktyczne wskazówki',
                'type' => 'news',
                'estimated_time' => 8,
                'tags' => ['Frontend', 'JavaScript', 'Vue'],
                'content' => <<<'MD'
Wydajność frontendu bezpośrednio wpływa na doświadczenie użytkownika i konwersję. Oto sprawdzone techniki optymalizacji.

## Core Web Vitals
Google mierzy trzy kluczowe metryki:
- **LCP (Largest Contentful Paint)** - czas renderowania największego elementu. Cel: poniżej 2.5s.
- **FID (First Input Delay)** / **INP (Interaction to Next Paint)** - czas odpowiedzi na interakcję użytkownika. Cel: poniżej 100ms.
- **CLS (Cumulative Layout Shift)** - stabilność layoutu. Cel: poniżej 0.1.

## Lazy Loading
Ładuj komponenty i obrazy dopiero wtedy, gdy są potrzebne. W Vue: `defineAsyncComponent()`. W HTML: `<img loading="lazy">`.

## Bundle size
- Analizuj rozmiar bundle'a (`vite-bundle-analyzer`).
- Używaj **tree shaking** - importuj tylko to, czego używasz: `import { debounce } from 'lodash-es'` zamiast `import _ from 'lodash'`.
- Rozważ code splitting - dynamiczne importy (`import()`) dla rzadko używanych ścieżek.

## Cachowanie
- Ustawiaj właściwe nagłówki cache dla zasobów statycznych.
- Używaj **content hashing** w nazwach plików (Vite robi to automatycznie), by długo cachować pliki CSS/JS.

## Obrazy
- Używaj nowoczesnych formatów: **WebP** lub **AVIF**.
- Serwuj odpowiedni rozmiar za pomocą atrybutu `srcset`.
- Kompresuj obrazy przed deploymentem.

## Renderowanie po stronie serwera (SSR)
Dla stron z dużą ilością treści rozważ SSR (Nuxt, Next.js), by przyspieszyć czas do pierwszego wyrenderowania i poprawić SEO.
MD,
            ],

            // --- PHP / LARAVEL ---
            [
                'title' => 'Wzorzec Repository w Laravel - kiedy i jak stosować',
                'type' => 'article',
                'estimated_time' => 7,
                'tags' => ['PHP', 'Laravel', 'Backend'],
                'content' => <<<'MD'
Wzorzec Repository oddziela warstwę dostępu do danych od logiki biznesowej, co ułatwia testowanie i zmianę źródła danych.

## Dlaczego Repository?
Typowy kontroler w Laravel często miesza logikę biznesową z wywołaniami Eloquent. Gdy chcemy przetestować logikę biznesową bez bazy danych, napotkamy problem. Repository rozwiązuje to przez dostarczenie abstrakcji nad źródłem danych.

## Implementacja
```php
// Interfejs
interface UserRepositoryInterface {
    public function findById(int $id): ?User;
    public function findByEmail(string $email): ?User;
    public function create(array $data): User;
}

// Implementacja Eloquent
class EloquentUserRepository implements UserRepositoryInterface {
    public function findById(int $id): ?User {
        return User::find($id);
    }
    // ...
}
```

## Bindowanie w IoC Container
```php
// AppServiceProvider
$this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
```

## Korzyści
- **Testowalność** - w testach możesz podmienić repozytorium na mock/stub.
- **Zamiana źródła danych** - zmiana z Eloquent na API lub inną bazę danych bez modyfikacji logiki biznesowej.
- **Czytelność** - kontrolery stają się cienkie i skupiają się na obsłudze HTTP.

## Kiedy NIE używać Repository?
W mniejszych projektach wzorzec Repository może być **over-engineering**. Laravel i Eloquent są już abstrakcją nad bazą danych. Używaj Repository, gdy projekt jest złożony, wymaga intensywnego testowania lub planowane jest wsparcie wielu źródeł danych.
MD,
            ],
            [
                'title' => 'REST API - zasady projektowania i dobre praktyki',
                'type' => 'article',
                'estimated_time' => 8,
                'tags' => ['Backend', 'PHP', 'Laravel'],
                'content' => <<<'MD'
Dobrze zaprojektowane API to takie, którego używanie jest intuicyjne i spójne. Oto zasady REST, które warto stosować.

## Zasoby, nie akcje
URL-e powinny reprezentować zasoby (rzeczowniki), a metody HTTP definiują akcje:
- `GET /users` - lista użytkowników
- `GET /users/42` - pojedynczy użytkownik
- `POST /users` - tworzenie użytkownika
- `PUT /users/42` - aktualizacja (pełna)
- `PATCH /users/42` - aktualizacja (częściowa)
- `DELETE /users/42` - usunięcie

Unikaj: `POST /createUser`, `GET /deleteUser?id=42`.

## Kody odpowiedzi HTTP
Używaj właściwych kodów statusu:
- `200 OK` - sukces
- `201 Created` - zasób został utworzony
- `204 No Content` - sukces bez treści odpowiedzi (np. po DELETE)
- `400 Bad Request` - błędne dane wejściowe
- `401 Unauthorized` - brak uwierzytelnienia
- `403 Forbidden` - brak uprawnień
- `404 Not Found` - zasób nie istnieje
- `422 Unprocessable Entity` - błąd walidacji
- `500 Internal Server Error` - błąd serwera

## Wersjonowanie
Wersjonuj API od samego początku: `/api/v1/users`. Ułatwia to wprowadzanie zmian bez łamania istniejących klientów.

## Paginacja i filtrowanie
- Paginacja: `GET /articles?page=2&per_page=20`
- Filtrowanie: `GET /articles?tag=PHP&published=true`
- Sortowanie: `GET /articles?sort=created_at&order=desc`

## Bezpieczeństwo
- Zawsze używaj HTTPS.
- Waliduj dane wejściowe po stronie serwera.
- Stosuj rate limiting, by chronić API przed nadużyciami.
- Nie zwracaj wrażliwych danych (hasła, tokeny) w odpowiedziach.
MD,
            ],
        ];

        foreach ($articles as $data) {
            $tagNames = $data['tags'];
            unset($data['tags']);

            $article = Article::create(array_merge($data, [
                'user_id' => $userId,
                'is_published' => true,
            ]));

            $tagIds = Tag::whereIn('name', $tagNames)->pluck('id');
            $article->tags()->attach($tagIds);
        }
    }
}
