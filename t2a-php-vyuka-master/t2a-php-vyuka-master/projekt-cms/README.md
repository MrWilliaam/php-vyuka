# CMS – 2. fáze (PHP + SQLite)

Připravený základ pro studenty, kteří si pro 2. fázi projektu zvolili **vlastní téma** s CMS-podobnou funkcionalitou (web kapely, hasičského sboru, sportovního klubu, školního magazínu…).

Tato kostra je **doménově neutrální** – vzorová data odpovídají generickému spolkovému/klubovému webu. Stačí ji nahrát do svého repa a podle pokynů níže upravit data tak, aby odpovídala tvému tématu.

## Co kostra obsahuje

- **Články** s kategoriemi, štítky (M:N), autorem, stavem (koncept/publikováno) a obrázkem
- **Komentáře** pod články (anonymní – jméno + e-mail + text, schvalování adminem)
- **Administrátorské přihlášení** přes session a `password_hash()` / `password_verify()`
- **Události** (kalendář akcí) s datem, místem a popisem
- **Validátor** formulářů s fluent API (kostra k doplnění studentem)

## Spuštění

```bash
php projekt-cms/database/init.php
php -S localhost:8080 -t projekt-cms
```

Otevři v prohlížeči: `http://localhost:8080/ukazka.php`

**Přihlašovací údaje admina** (vytvořené v `init.php`):
- E-mail: `admin@cms.cz`
- Heslo: `admin123`

Ukázkové stránky:
- `/ukazka.php` – homepage (články, události, tag cloud)
- `/clanek.php?slug=…` – detail článku + komentáře + formulář pro nový komentář
- `/admin-login.php` – přihlašovací formulář
- `/admin.php` – administrace (chráněná, vyžaduje login) – přehled článků + moderace komentářů

### Reset databáze

```bash
php projekt-cms/database/init.php
```

## Struktura projektu

```
projekt-cms/
├── database/
│   ├── init.php              ← skript pro vytvoření/reset databáze
│   └── cms.db                ← SQLite databáze (generuje se automaticky)
├── src/
│   ├── bootstrap.php         ← načte všechny třídy
│   ├── Database.php          ← připojení k databázi
│   ├── Auth.php              ← přihlášení admina (session + password_verify)
│   ├── Validator.php         ← validátor formulářů (fluent interface)
│   ├── DTO/                  ← datové objekty (readonly)
│   │   ├── UserDTO.php
│   │   ├── CategoryDTO.php
│   │   ├── TagDTO.php
│   │   ├── ArticleDTO.php
│   │   ├── CommentDTO.php
│   │   └── EventDTO.php
│   └── Repository/           ← třídy pro práci s databází
│       ├── UserRepository.php
│       ├── CategoryRepository.php
│       ├── TagRepository.php
│       ├── ArticleRepository.php
│       ├── CommentRepository.php
│       └── EventRepository.php
├── partials/
│   ├── header.php            ← hlavička s navigací a indikací přihlášení
│   ├── footer.php            ← patička
│   └── article-card.php      ← karta článku
├── assets/
│   ├── css/                  ← placeholder CSS (nahraď vlastním z 1. fáze)
│   └── images/
│       ├── clanky/           ← obrázky článků
│       └── events/           ← obrázky událostí
├── ukazka.php                ← homepage (články + události + tagy)
├── clanek.php                ← detail článku + komentáře
├── admin-login.php           ← přihlašovací formulář
├── admin.php                 ← administrace (chráněná)
├── admin-logout.php          ← odhlášení
└── README.md                 ← tento soubor
```

## Databázové tabulky

| Tabulka | Popis |
|---------|-------|
| `users` | Administrátoři (e-mail, hash hesla, jméno) |
| `categories` | Kategorie/sekce článků (Aktuality, Reportáže, …) |
| `tags` | Štítky pro články |
| `articles` | Články (titulek, slug, perex, obsah, cover, status, datum publikace, autor, kategorie) |
| `article_tags` | Vazební tabulka M:N (článek ↔ tag) |
| `comments` | Komentáře pod články (jméno, e-mail, text, status pending/approved) |
| `events` | Kalendář událostí (titulek, datum, místo, popis, obrázek) |

## Klíčové třídy

### `Auth` – přihlášení administrátora

```php
$auth = new Auth();

// přihlášení
if ($auth->login($email, $password)) {
    header('Location: admin.php');
    exit;
}

// chráněná stránka
$auth->requireLogin(); // přesměruje na admin-login.php pokud nikdo nepřihlášen

// dotazy
$auth->isLoggedIn();         // bool
$auth->getCurrentUser();     // ?UserDTO
$auth->getUserId();          // ?int

// odhlášení
$auth->logout();
```

### `ArticleRepository` – CRUD nad články včetně tagů

```php
$articleRepo = new ArticleRepository();
$tagRepo = new TagRepository();

// Veřejné dotazy (jen publikované)
$articles = $articleRepo->getPublished(limit: 10);
$inCategory = $articleRepo->getByCategorySlug('aktuality');
$byTag = $articleRepo->getByTagSlug('akce');
$results = $articleRepo->search('soustředění');

// Pro admin (i koncepty)
$all = $articleRepo->getAll();
$article = $articleRepo->getBySlug('vitejte');

// Vytvoření článku s tagy (transakce)
$article = $articleRepo->create(
    categoryId: 1,
    authorId: $auth->getUserId(),
    title: 'Nový článek',
    slug: 'novy-clanek',
    perex: 'Krátký souhrn…',
    content: 'Plný obsah článku.',
    coverImage: 'assets/images/clanky/novy.svg',
    status: 'published',
    publishedAt: date('Y-m-d H:i:s'),
    tagIds: [1, 3, 7],
);

// Editace
$articleRepo->update($id, ...);

// Smazání (kaskádově smaže i komentáře a vazby na tagy)
$articleRepo->delete($id);

// Tagy k článku (kvůli M:N je to druhý dotaz)
$tags = $tagRepo->getForArticle($article->id);
```

### `CommentRepository` – komentáře

```php
$commentRepo = new CommentRepository();

// Veřejné
$approved = $commentRepo->getApprovedForArticle($article->id);

// Vytvoření (od nepřihlášeného uživatele – status "pending")
$comment = $commentRepo->create(
    articleId: $article->id,
    authorName: $name,
    authorEmail: $email,
    content: $text,
);

// Admin moderace
$pending = $commentRepo->getPending();
$commentRepo->approve($id);
$commentRepo->delete($id);
```

### `EventRepository` – události

```php
$eventRepo = new EventRepository();

$upcoming = $eventRepo->getUpcoming(limit: 5);
$past = $eventRepo->getPast();
$event = $eventRepo->getBySlug('letni-soustredeni-2026');
```

### `TagRepository` – tagy a tag cloud

```php
$tagRepo = new TagRepository();

$allTags = $tagRepo->getAll();
$tags = $tagRepo->getForArticle($articleId);   // tagy konkrétního článku

// Tag cloud (tagy + počet publikovaných článků)
$cloud = $tagRepo->getTagCloud();
// pole [['tag' => TagDTO, 'count' => 3], ...]
```

## Jak přizpůsobit pro vlastní téma

Kód kostry **nepotřebuje měnit** – stačí upravit vzorová data v `database/init.php`. Tady jsou návody pro některá témata:

### Web kapely

| Tabulka | Co tam dát |
|---------|-----------|
| `users` | členové kapely, kteří mají oprávnění psát novinky |
| `categories` | „Aktuality", „Recenze", „Koncerty", „Diskografie", „Tour" |
| `tags` | „nové album", „singl", „interview", „live", „2026" |
| `articles` | „Vychází nový singl Y", „Reportáž z koncertu v…", „Tour přes Česko 2026" |
| `events` | jednotlivé koncerty (název = klub, datum, místo = město, popis = předkapely, čas, vstup) |
| `comments` | reakce fanoušků pod články |

### Web hasičů (SDH)

| Tabulka | Co tam dát |
|---------|-----------|
| `users` | velitel, starosta sboru, kdo má přístup do administrace |
| `categories` | „Aktuality", „Zásahy", „Cvičení", „Soutěže", „Mladí hasiči", „Sbor" |
| `tags` | „požár", „technická pomoc", „výjezd", „závody", „2026", „MTZ" |
| `articles` | „Výjezd k požáru lesa u…", „Účast na okrskové soutěži", „Pozvánka na pochod" |
| `events` | tréninky, soutěže, plánovaná cvičení, dětský den (datum + místo) |
| `comments` | dotazy a reakce občanů |

### Sportovní klub, školní magazín, spolek atd.

Postup je stejný — uprav názvy kategorií, štítků a textů článků v `database/init.php` tak, aby odpovídaly tvému tématu. Schéma databáze je dostatečně obecné, aby pokrylo prakticky jakýkoliv obsahový web s administrací.

### Konkrétní postup adaptace dat

1. Otevři `database/init.php` a najdi sekci `Kategorie`, `Tagy`, `Články`, `Události`.
2. Přepiš obsah těchto polí pro své téma. Slug (URL identifikátor) by neměl obsahovat diakritiku ani mezery (jen `[a-z0-9-]`).
3. Nahraď placeholder obrázky v `assets/images/clanky/` a `assets/images/events/` vlastními fotkami a uprav cesty v `init.php`.
4. Změň název v `partials/header.php` a `partials/footer.php` (logo a copyright).
5. Spusť `php projekt-cms/database/init.php` pro znovuvytvoření databáze.

### Co k tématu klidně přidej (bonusy)

Pokud chceš jít dál nad rámec kostry, můžou se ti hodit další entity a funkce:
- **Galerie fotek** (album → fotky), případně lightbox
- **Členové kapely / sboru** (jméno, role/nástroj, fotka, popis)
- **Soubory ke stažení** (PDF s programem, ICS kalendář, MP3 ukázky…)
- **RSS feed** pro články
- **Vyhledávání** – třída `ArticleRepository::search()` je už hotová
- **Stránkování** výpisu článků (`?page=N`)
- **Newsletter** (jednoduchý seznam e-mailů)

## Bezpečnost – co je už hotové a co dořešit

### Hotové
- **Hashing hesel** přes `password_hash` / `password_verify` (Auth.php)
- **Regenerace ID session** po loginu a logoutu (proti session fixation)
- **Prepared statements** ve všech repozitářích (proti SQL injection)
- **`htmlspecialchars`** ve všech výpisech v ukázkových šablonách (proti XSS)

### Musíš ještě dořešit ty
- **CSRF tokeny** u formulářů, které mění stav (komentář, login, CRUD článků). Ukázkový kód má pro jednoduchost vynechány – ve své práci je implementuj. Příklad:

```php
// Vygenerování tokenu na začátku stránky s formulářem
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Skryté pole ve formuláři
<input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

// Ověření při zpracování
if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    exit('Neplatný bezpečnostní token.');
}
```

- **Validace na úrovni Validator třídy** – `Validator.php` má kostru se stub metodami a TODO komentáři. Dopiš implementace metod `required`, `email`, `maxLength`, `date`, `pattern`, `in`, `isValid`, `getErrors`, `getError`, `hasError` (vzor `minLength` už máš).
- **Antispam u komentářů** – minimálně honeypot pole (skryté input field, které musí zůstat prázdné) nebo časová prodleva (mezi zobrazením formuláře a odesláním musí uplynout aspoň pár sekund).
- **Rate limiting** přihlašování – po N neúspěšných pokusech omez login na X minut (jednoduchá implementace přes session nebo tabulku v DB).

## Stránky, které si musíš dopsat sám

Kostra ti dává základ – konkrétní stránky (`index.php`, `clanky.php`, `kategorie.php`, `udalosti.php`, `o-nas.php`, `kontakt.php`, `admin-clanek-novy.php`, `admin-clanek-edit.php`, `404.php`, atd.) napíšeš sám podle vzoru ukázkových stránek.

### Co potřebuješ pro funkční CMS minimálně

| Stránka | Co dělá |
|---------|---------|
| `index.php` | Homepage: pár nejnovějších článků, sidebar s nadcházejícími událostmi |
| `clanky.php` | Výpis všech publikovaných článků, filtrování přes `?category=…` a `?tag=…` |
| `clanek.php` | (hotová ukázka) Detail článku + komentáře + formulář |
| `udalosti.php` | Výpis událostí (nadcházející + proběhlé) |
| `vyhledavani.php` | `ArticleRepository::search($q)` + zobrazení výsledků |
| `o-nas.php`, `kontakt.php` | Obsahové stránky s formulářem (kontaktní formulář s validací) |
| `404.php` | Chybová stránka |
| `admin-login.php` | (hotová ukázka) |
| `admin.php` | (hotová ukázka) Nástěnka |
| `admin-clanek-novy.php` | Formulář pro nový článek (kategorie, tagy multi-select, validace) |
| `admin-clanek-edit.php` | Stejný formulář, ale předvyplněný hodnotami |
| `admin-clanek-smazat.php` | Smazání článku po potvrzení |
| `admin-logout.php` | (hotová ukázka) |

Tří hlavních formulářů — login, komentář, editace článku — využij k procvičení třídy `Validator`. Vzor použití má e-shop kostra v `projektPHP.html`.
