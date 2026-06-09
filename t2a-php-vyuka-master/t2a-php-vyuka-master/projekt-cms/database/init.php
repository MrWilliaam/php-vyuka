<?php

declare(strict_types=1);

/**
 * Inicializace databáze – vytvoří tabulky a naplní vzorovými daty.
 *
 * Spuštění: php projekt-cms/database/init.php
 *
 * POZOR: Smaže existující databázi a vytvoří novou!
 *
 * Přihlašovací údaje admina:
 *   E-mail: admin@cms.cz
 *   Heslo:  admin123
 */

$dbPath = __DIR__ . '/cms.db';

// Smazat existující databázi
if (file_exists($dbPath)) {
	unlink($dbPath);
	echo "Stará databáze smazána.\n";
}

$db = new PDO('sqlite:' . $dbPath, options: [
	PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$db->exec('PRAGMA journal_mode = WAL');
$db->exec('PRAGMA foreign_keys = ON');

// ============================================================
// Vytvoření tabulek
// ============================================================

$db->exec('
    CREATE TABLE users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        email TEXT NOT NULL UNIQUE,
        password_hash TEXT NOT NULL,
        name TEXT NOT NULL,
        created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
    )
');

$db->exec('
    CREATE TABLE categories (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        slug TEXT NOT NULL UNIQUE,
        description TEXT NOT NULL DEFAULT ""
    )
');

$db->exec('
    CREATE TABLE tags (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        slug TEXT NOT NULL UNIQUE
    )
');

$db->exec('
    CREATE TABLE articles (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        category_id INTEGER NOT NULL,
        author_id INTEGER NOT NULL,
        title TEXT NOT NULL,
        slug TEXT NOT NULL UNIQUE,
        perex TEXT NOT NULL DEFAULT "",
        content TEXT NOT NULL,
        cover_image TEXT NOT NULL DEFAULT "",
        status TEXT NOT NULL DEFAULT "draft",
        published_at TEXT,
        created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES categories(id),
        FOREIGN KEY (author_id) REFERENCES users(id)
    )
');

$db->exec('
    CREATE TABLE article_tags (
        article_id INTEGER NOT NULL,
        tag_id INTEGER NOT NULL,
        PRIMARY KEY (article_id, tag_id),
        FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
        FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
    )
');

$db->exec('
    CREATE TABLE comments (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        article_id INTEGER NOT NULL,
        author_name TEXT NOT NULL,
        author_email TEXT NOT NULL,
        content TEXT NOT NULL,
        status TEXT NOT NULL DEFAULT "pending",
        created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
    )
');

$db->exec('
    CREATE TABLE events (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        slug TEXT NOT NULL UNIQUE,
        event_date TEXT NOT NULL,
        location TEXT NOT NULL DEFAULT "",
        description TEXT NOT NULL DEFAULT "",
        image TEXT NOT NULL DEFAULT "",
        created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
    )
');

echo "Tabulky vytvořeny.\n";

// ============================================================
// Vzorová data – neutrální spolkový/klubový web
// (snadno se přizpůsobí na kapelu, hasiče nebo jiné téma)
// ============================================================

// Administrátor – heslo: admin123
$adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
$db->prepare('
    INSERT INTO users (email, password_hash, name)
    VALUES (?, ?, ?)
')->execute(['admin@cms.cz', $adminPassword, 'Administrátor']);

echo "Administrátor vytvořen (admin@cms.cz / admin123).\n";

// Kategorie
$categories = [
	['Aktuality', 'aktuality', 'Nejnovější zprávy a oznámení.'],
	['Reportáže', 'reportaze', 'Reportáže z proběhlých akcí a událostí.'],
	['Plánujeme', 'planujeme', 'Co nás čeká v nejbližší době.'],
	['Z archivu', 'z-archivu', 'Starší články a vzpomínky.'],
];

$catStmt = $db->prepare('INSERT INTO categories (name, slug, description) VALUES (?, ?, ?)');
foreach ($categories as $cat) {
	$catStmt->execute($cat);
}

echo "Kategorie vloženy.\n";

// Tagy
$tags = [
	['Novinka', 'novinka'],
	['Důležité', 'dulezite'],
	['Akce', 'akce'],
	['Reportáž', 'reportaz'],
	['Soutěž', 'soutez'],
	['Foto', 'foto'],
	['Rok 2026', 'rok-2026'],
	['Pozvánka', 'pozvanka'],
];

$tagStmt = $db->prepare('INSERT INTO tags (name, slug) VALUES (?, ?)');
foreach ($tags as $tag) {
	$tagStmt->execute($tag);
}

echo "Tagy vloženy.\n";

// Články – [category_id, title, slug, perex, content, cover_image, status, published_at]
$articles = [
	// Aktuality (1)
	[
		1, 'Vítejte na našich nových stránkách',
		'vitejte-na-novych-strankach',
		'Spustili jsme nový web. Najdete tu aktuality, reportáže z akcí, kalendář a komentáře.',
		"Po několika měsících příprav jsme spustili nové webové stránky.\n\nNajdete tu pravidelné aktuality, fotoreportáže z akcí, kalendář nadcházejících událostí a možnost zapojit se do diskuzí pod jednotlivými články. Doufáme, že vám nové stránky přinesou přehlednější a aktuálnější informace, než jste byli zvyklí.\n\nPokud objevíte chybu nebo máte tip na zlepšení, dejte nám vědět.",
		'assets/images/clanky/uvod.svg', 'published', '2026-04-15 09:00:00',
	],
	[
		1, 'Důležité oznámení pro členy',
		'dulezite-oznameni-pro-cleny',
		'Změna v termínu pravidelné schůzky – přesouváme ji o týden později.',
		"Vzhledem ke kolizi s jinou akcí jsme se rozhodli přesunout pravidelnou schůzku o jeden týden později.\n\nNový termín: čtvrtek 21. května 2026 v 18:00 v obvyklých prostorách.\n\nProgram zůstává beze změn. Pokud se nemůžete účastnit, dejte prosím vědět vedení.",
		'assets/images/clanky/oznameni.svg', 'published', '2026-05-02 14:30:00',
	],
	[
		1, 'Hledáme nové členy',
		'hledame-nove-cleny',
		'Otevíráme přihlášky pro nové členy na jarní sezónu 2026.',
		"Otevíráme přihlášky pro nové členy na jarní sezónu 2026.\n\nUvítáme nadšence všech věkových kategorií. Předchozí zkušenosti nejsou podmínkou – důležité je nadšení a chuť zapojit se.\n\nPřihlášky přijímáme do konce května. Kontaktujte nás přes formulář nebo přímo na e-mail.",
		'assets/images/clanky/clenove.svg', 'published', '2026-05-05 10:15:00',
	],

	// Reportáže (2)
	[
		2, 'Reportáž z výroční akce',
		'reportaz-z-vyrocni-akce',
		'Letošní výroční akce se vydařila – přišlo víc lidí než kdykoli předtím.',
		"V sobotu 12. dubna proběhla naše tradiční výroční akce. Počasí nám přálo a účast překonala všechna očekávání – sešlo se přes sto lidí.\n\nProgram zahrnoval prezentaci úspěchů uplynulého roku, společné posezení a malou výstavu fotografií. Děkujeme všem, kteří se podíleli na přípravě, i všem, kdo přišli.\n\nKompletní galerii fotek najdete na stránkách v sekci Foto.",
		'assets/images/clanky/vyrocni.svg', 'published', '2026-04-15 18:00:00',
	],
	[
		2, 'Účast na regionálním setkání',
		'ucast-na-regionalnim-setkani',
		'V březnu jsme se zúčastnili regionálního setkání. Přivezli jsme dobré tipy i ocenění.',
		"Začátkem března se konalo regionální setkání, kterého jsme se aktivně zúčastnili.\n\nKromě cenné výměny zkušeností se nám podařilo získat čestné uznání za nejlepší prezentaci. Přivezli jsme i řadu nápadů, které postupně zavádíme do našich aktivit.\n\nKomu setkání uniklo, najde tu klíčové informace shrnuté v krátkém článku.",
		'assets/images/clanky/setkani.svg', 'published', '2026-03-22 16:00:00',
	],
	[
		2, 'Zápis z jarního výjezdu',
		'zapis-z-jarniho-vyjezdu',
		'Třídenní jarní výjezd – co se povedlo, co příště vylepšíme.',
		"Třídenní jarní výjezd máme za sebou. Až na drobné komplikace s počasím první den se vše vydařilo.\n\nSpolečně jsme prošli plánovaný program, zvládli jsme jak teoretickou, tak praktickou část. Pro příště si pamatujeme: víc náhradního oblečení, dřívější odjezd a vlastní zásoby svačiny – v místních obchodech není moc na výběr.\n\nDíky všem, kteří se podíleli na organizaci.",
		'assets/images/clanky/vyjezd.svg', 'published', '2026-04-30 20:00:00',
	],

	// Plánujeme (3)
	[
		3, 'Letní soustředění 2026',
		'letni-soustredeni-2026',
		'Letní soustředění se uskuteční v termínu 1.–7. července 2026. Otevíráme přihlášky.',
		"Letní soustředění proběhne v termínu 1.–7. července 2026 v osvědčeném prostředí, které dobře znáte z minulých let.\n\nPřihlášky přijímáme do 15. června. Cena zahrnuje plnou penzi, ubytování a celý program. Členové mají slevu 20 %.\n\nVšechny podrobnosti najdete v přihlašovacím formuláři.",
		'assets/images/clanky/soustredeni.svg', 'published', '2026-05-10 08:30:00',
	],
	[
		3, 'Pozvánka na květnovou akci',
		'pozvanka-na-kvetnovou-akci',
		'V sobotu 23. května pořádáme otevřenou akci pro veřejnost.',
		"Srdečně zveme všechny příznivce na otevřenou akci, která se uskuteční v sobotu 23. května od 10:00.\n\nPřipraven je program pro děti i dospělé, drobné občerstvení a možnost se s námi seznámit. Vstup je zdarma.\n\nAkce proběhne v obvyklém místě, v případě nepříznivého počasí v náhradních prostorách.",
		'assets/images/clanky/pozvanka.svg', 'published', '2026-05-08 12:00:00',
	],

	// Z archivu (4)
	[
		4, 'Ohlédnutí za rokem 2025',
		'ohlednuti-za-rokem-2025',
		'Co se nám v uplynulém roce povedlo a co bychom rádi zlepšili.',
		"Rok 2025 byl pro nás úspěšný. Uspořádali jsme celkem 14 akcí, přivítali 22 nových členů a poprvé jsme se účastnili mezinárodní soutěže.\n\nMezi vrcholy patří jarní turné, podzimní reprezentační akce a výroba nového propagačního materiálu. Mrzí nás, že jsme nestihli dokončit modernizaci klubovny – to je úkol pro letošek.\n\nDěkujeme všem členům i podporovatelům.",
		'assets/images/clanky/ohlednuti.svg', 'published', '2026-01-10 09:00:00',
	],
	[
		4, 'Z historie spolku',
		'z-historie-spolku',
		'Stručná historie – jak vše začalo a kde jsme dnes.',
		"Spolek vznikl před více než dvaceti lety z iniciativy několika nadšenců.\n\nPrvních pár let bylo náročných – chyběly prostory i finance. Postupně se ale podařilo získat zázemí, přilákat členy a vybudovat tradice, na kterých dnes stavíme.\n\nTento článek shrnuje klíčové milníky v naší historii a uvádí jména lidí, bez kterých bychom dnes nebyli, kde jsme.",
		'assets/images/clanky/historie.svg', 'published', '2025-12-20 14:00:00',
	],

	// Příklad konceptu (nepublikováno) – ukázka stavu "draft"
	[
		1, 'Pracovní verze – brzy zveřejníme',
		'pracovni-verze-clanku',
		'Tento článek je zatím v pracovní verzi.',
		"Obsah článku ještě dolaďujeme.",
		'', 'draft', NULL,
	],
];

$artStmt = $db->prepare('
    INSERT INTO articles (category_id, author_id, title, slug, perex, content, cover_image, status, published_at)
    VALUES (?, 1, ?, ?, ?, ?, ?, ?, ?)
');

foreach ($articles as $a) {
	$artStmt->execute($a);
}

echo "Články vloženy (" . count($articles) . ").\n";

// Mapování článku ↔ tagy (M:N)
$articleTags = [
	// článek 1 (Vítejte)
	[1, 1], [1, 7],
	// článek 2 (Důležité oznámení)
	[2, 2], [2, 8],
	// článek 3 (Hledáme nové členy)
	[3, 1], [3, 2],
	// článek 4 (Reportáž z výroční akce)
	[4, 4], [4, 6], [4, 7],
	// článek 5 (Účast na regionálním setkání)
	[5, 4], [5, 3],
	// článek 6 (Zápis z jarního výjezdu)
	[6, 4], [6, 7],
	// článek 7 (Letní soustředění)
	[7, 3], [7, 8], [7, 7],
	// článek 8 (Pozvánka na květnovou akci)
	[8, 3], [8, 8],
	// článek 9 (Ohlédnutí za rokem 2025)
	[9, 4],
	// článek 10 (Z historie spolku) – bez tagů
];

$atStmt = $db->prepare('INSERT INTO article_tags (article_id, tag_id) VALUES (?, ?)');
foreach ($articleTags as $at) {
	$atStmt->execute($at);
}

echo "Propojení článek↔tag vloženo (" . count($articleTags) . ").\n";

// Komentáře
$comments = [
	// k článku 1
	[1, 'Petr Novák', 'petr@example.cz', 'Skvělé, konečně přehledný web! Díky za odvedenou práci.', 'approved', '2026-04-15 10:30:00'],
	[1, 'Jana K.', 'jana@example.cz', 'Hezký design. Bude tu RSS feed?', 'approved', '2026-04-16 08:00:00'],
	// k článku 2
	[2, 'Milan H.', 'milan@example.cz', 'Díky za informaci, do diáře přepsáno.', 'approved', '2026-05-03 19:15:00'],
	[2, 'spam bot', 'spam@spam.com', 'Buy our pills now!!! click here', 'pending', '2026-05-04 02:45:00'],
	// k článku 4 (reportáž)
	[4, 'Veronika S.', 'veronika@example.cz', 'Krásné fotky! Byla jsem tam, opravdu super akce.', 'approved', '2026-04-16 11:20:00'],
	[4, 'Tomáš L.', 'tomas@example.cz', 'Bohužel jsem nestihl, doufám, že příští rok.', 'approved', '2026-04-17 14:00:00'],
	[4, 'Ivana M.', 'ivana@example.cz', 'Děkuji organizátorům, bylo to perfektní.', 'pending', '2026-04-20 09:30:00'],
	// k článku 7 (Letní soustředění)
	[7, 'Karel B.', 'karel@example.cz', 'Jsou ještě volná místa? Rád bych vzal i syna (12 let).', 'approved', '2026-05-11 17:00:00'],
	[7, 'Lucie P.', 'lucie@example.cz', 'Hlásím sebe a manžela.', 'approved', '2026-05-12 09:15:00'],
];

$comStmt = $db->prepare('
    INSERT INTO comments (article_id, author_name, author_email, content, status, created_at)
    VALUES (?, ?, ?, ?, ?, ?)
');
foreach ($comments as $c) {
	$comStmt->execute($c);
}

echo "Komentáře vloženy (" . count($comments) . ").\n";

// Události (kalendář)
$events = [
	['Pravidelná schůzka', 'schuzka-kveten', '2026-05-21', 'Klubovna spolku', 'Měsíční schůzka členů. Program: shrnutí dubna, plán na červen, různé.', 'assets/images/events/schuzka.svg'],
	['Otevřená akce pro veřejnost', 'otevrena-akce-23-5', '2026-05-23', 'Náměstí v centru', 'Den otevřených dveří s programem pro děti i dospělé. Vstup zdarma.', 'assets/images/events/otevreno.svg'],
	['Letní soustředění', 'letni-soustredeni-2026', '2026-07-01', 'Středisko Lesní zátiší', 'Týdenní letní soustředění s plnou penzí. Termín 1.–7. července.', 'assets/images/events/soustredeni.svg'],
	['Reprezentační akce', 'reprezentacni-akce-2026', '2026-09-12', 'Sportovní hala', 'Tradiční podzimní reprezentace spolku.', 'assets/images/events/reprezentace.svg'],
	['Výroční schůze', 'vyrocni-schuze-2026', '2026-11-15', 'Klubovna spolku', 'Výroční schůze s volbou nového vedení.', 'assets/images/events/vyrocni.svg'],
	['Vánoční setkání', 'vanocni-setkani-2026', '2026-12-18', 'Klubovna spolku', 'Neformální vánoční setkání členů a přátel.', 'assets/images/events/vanocni.svg'],
];

$evStmt = $db->prepare('
    INSERT INTO events (title, slug, event_date, location, description, image)
    VALUES (?, ?, ?, ?, ?, ?)
');
foreach ($events as $e) {
	$evStmt->execute($e);
}

echo "Události vloženy (" . count($events) . ").\n";

// Indexy
$db->exec('CREATE INDEX idx_articles_category ON articles(category_id)');
$db->exec('CREATE INDEX idx_articles_slug ON articles(slug)');
$db->exec('CREATE INDEX idx_articles_status ON articles(status)');
$db->exec('CREATE INDEX idx_articles_published_at ON articles(published_at)');
$db->exec('CREATE INDEX idx_categories_slug ON categories(slug)');
$db->exec('CREATE INDEX idx_tags_slug ON tags(slug)');
$db->exec('CREATE INDEX idx_comments_article ON comments(article_id)');
$db->exec('CREATE INDEX idx_comments_status ON comments(status)');
$db->exec('CREATE INDEX idx_events_date ON events(event_date)');

echo "\nDatabáze úspěšně inicializována!\n";
echo "Soubor: $dbPath\n";
echo "\nPřihlašovací údaje admina:\n";
echo "  E-mail: admin@cms.cz\n";
echo "  Heslo:  admin123\n";
