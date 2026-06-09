<?php

declare(strict_types=1);

/**
 * UKÁZKOVÁ STRÁNKA – homepage CMS
 *
 * Spuštění:
 *   1. Nejdřív vytvořte databázi:  php projekt-cms/database/init.php
 *   2. Spusťte PHP server:         php -S localhost:8080 -t projekt-cms
 *   3. Otevřete v prohlížeči:      http://localhost:8080/ukazka.php
 *
 * Co tato stránka ukazuje:
 *   - Načtení všech tříd přes bootstrap.php
 *   - Znovupoužití částí stránky (header, footer, article-card) přes require
 *   - Práce s ArticleRepository (publikované články + tagy)
 *   - Práce s EventRepository (nadcházející události)
 *   - Práce s TagRepository (tag cloud)
 *   - Indikace přihlášeného admina v hlavičce (přes Auth)
 */

require_once __DIR__ . '/src/bootstrap.php';

$articleRepo = new ArticleRepository();
$tagRepo = new TagRepository();
$eventRepo = new EventRepository();
$auth = new Auth();

// Načteme nejnovější publikované články včetně jejich tagů
$articles = $articleRepo->getPublished(limit: 6);

// Doplníme tagy do každého ArticleDTO (M:N → druhý dotaz)
$articles = array_map(
	function (ArticleDTO $a) use ($tagRepo): ArticleDTO {
		return new ArticleDTO(
			id: $a->id, categoryId: $a->categoryId, authorId: $a->authorId,
			title: $a->title, slug: $a->slug, perex: $a->perex, content: $a->content,
			coverImage: $a->coverImage, status: $a->status, publishedAt: $a->publishedAt,
			createdAt: $a->createdAt,
			categoryName: $a->categoryName, categorySlug: $a->categorySlug,
			authorName: $a->authorName,
			tags: $tagRepo->getForArticle($a->id),
		);
	},
	$articles,
);

$upcomingEvents = $eventRepo->getUpcoming(limit: 3);
$tagCloud = $tagRepo->getTagCloud();

$pageTitle = 'CMS – ukázka';
$currentUser = $auth->getCurrentUser();

?>
<?php require __DIR__ . '/partials/header.php'; ?>

<main class="container">
    <h1 class="section-title">Nejnovější články</h1>

    <div class="articles-grid">
        <?php foreach ($articles as $article): ?>
            <?php require __DIR__ . '/partials/article-card.php'; ?>
        <?php endforeach; ?>
    </div>

    <aside class="sidebar">
        <section class="sidebar__section">
            <h2>Nadcházející události</h2>
            <?php if ($upcomingEvents === []): ?>
                <p>Žádné nadcházející události.</p>
            <?php else: ?>
                <ul class="event-list">
                    <?php foreach ($upcomingEvents as $event): ?>
                        <li>
                            <strong><?= htmlspecialchars($event->getFormattedDate()) ?></strong>
                            – <?= htmlspecialchars($event->title) ?>
                            <span class="event-list__location">(<?= htmlspecialchars($event->location) ?>)</span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>

        <section class="sidebar__section">
            <h2>Štítky</h2>
            <div class="tag-cloud">
                <?php foreach ($tagCloud as $item): ?>
                    <?php if ($item['count'] > 0): ?>
                        <span class="tag-cloud__item">
                            <?= htmlspecialchars($item['tag']->name) ?>
                            <small>(<?= $item['count'] ?>)</small>
                        </span>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </section>
    </aside>

    <div class="info-box">
        <h2>Jak fungují partials?</h2>
        <p>Stejně jako u jiných projektů — <code>require __DIR__ . '/partials/article-card.php'</code> ve smyčce vloží kartu pro každý článek.</p>

        <h2>Co je tady navíc?</h2>
        <ul>
            <li><strong>Many-to-many tagy</strong> (článek ↔ tag) – nový vzor proti e-shopu. Druhý dotaz <code>$tagRepo-&gt;getForArticle($id)</code> doplní tagy k článku.</li>
            <li><strong>Auth</strong> – přihlášení administrátora přes session. Detail v <code>admin-login.php</code>.</li>
            <li><strong>Komentáře</strong> – formulář pro nepřihlášené, schvalování adminem. Detail v <code>clanek.php?slug=…</code>.</li>
        </ul>
    </div>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>
