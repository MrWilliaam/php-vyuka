<?php

declare(strict_types=1);

/**
 * UKÁZKOVÁ STRÁNKA – detail článku s komentáři a formulářem pro nový komentář
 *
 * Co tato stránka ukazuje:
 *   - Načtení článku podle slugu z URL (?slug=...)
 *   - Výpis schválených komentářů
 *   - Formulář pro nový komentář (kdokoliv, jméno + e-mail + text)
 *   - Post/Redirect/Get po odeslání formuláře
 *   - Ošetření 404 pro neexistující nebo nepublikované články
 *
 * Pozn.: validace formuláře je zde jen základní – studenti ji vylepší
 *        pomocí třídy Validator (viz Validator.php).
 */

require_once __DIR__ . '/src/bootstrap.php';

$articleRepo = new ArticleRepository();
$tagRepo = new TagRepository();
$commentRepo = new CommentRepository();
$auth = new Auth();

// Zpracování odeslaného komentáře (Post/Redirect/Get)
$commentErrors = [];
$commentFormData = ['name' => '', 'email' => '', 'content' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
	$articleId = (int) ($_POST['article_id'] ?? 0);
	$name = trim($_POST['name'] ?? '');
	$email = trim($_POST['email'] ?? '');
	$content = trim($_POST['content'] ?? '');

	// Ukázková (jednoduchá) validace – studenti ji nahradí třídou Validator
	if ($name === '') {
		$commentErrors['name'] = 'Jméno je povinné.';
	}
	if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$commentErrors['email'] = 'Neplatný e-mail.';
	}
	if (mb_strlen($content) < 3) {
		$commentErrors['content'] = 'Komentář musí mít alespoň 3 znaky.';
	}

	if ($commentErrors === []) {
		$commentRepo->create(
			articleId: $articleId,
			authorName: $name,
			authorEmail: $email,
			content: $content,
		);

		// Přesměrování s parametrem (aby šlo zobrazit hlášku "díky")
		header('Location: ' . $_SERVER['REQUEST_URI'] . '&posted=1');
		exit;
	}

	$commentFormData = ['name' => $name, 'email' => $email, 'content' => $content];
}

// Načtení článku podle slugu
$slug = trim($_GET['slug'] ?? '');
$article = $slug !== '' ? $articleRepo->getBySlug($slug) : NULL;

if ($article === NULL || !$article->isPublished()) {
	http_response_code(404);
	$pageTitle = 'Článek nenalezen';
	$currentUser = $auth->getCurrentUser();
	require __DIR__ . '/partials/header.php';
	echo '<main class="container"><h1>Článek nenalezen</h1><p>Zkuste se vrátit na <a href="index.php">hlavní stránku</a>.</p></main>';
	require __DIR__ . '/partials/footer.php';
	exit;
}

$tags = $tagRepo->getForArticle($article->id);
$comments = $commentRepo->getApprovedForArticle($article->id);

$pageTitle = $article->title . ' – CMS';
$currentUser = $auth->getCurrentUser();

?>
<?php require __DIR__ . '/partials/header.php'; ?>

<main class="container">
    <article class="article-detail">
        <?php if ($article->coverImage !== ''): ?>
            <img
                class="article-detail__image"
                src="<?= htmlspecialchars($article->coverImage) ?>"
                alt="<?= htmlspecialchars($article->title) ?>"
            >
        <?php endif; ?>

        <span class="article-detail__category">
            <?= htmlspecialchars($article->categoryName ?? '') ?>
            <?php if ($article->publishedAt !== NULL): ?>
                · <?= htmlspecialchars($article->getFormattedPublishedAt()) ?>
            <?php endif; ?>
            <?php if ($article->authorName !== NULL): ?>
                · <?= htmlspecialchars($article->authorName) ?>
            <?php endif; ?>
        </span>

        <h1 class="article-detail__title">
            <?= htmlspecialchars($article->title) ?>
        </h1>

        <p class="article-detail__perex">
            <?= htmlspecialchars($article->perex) ?>
        </p>

        <div class="article-detail__content">
            <?php foreach (explode("\n\n", $article->content) as $paragraph): ?>
                <p><?= nl2br(htmlspecialchars($paragraph)) ?></p>
            <?php endforeach; ?>
        </div>

        <?php if ($tags !== []): ?>
            <div class="article-detail__tags">
                <strong>Štítky:</strong>
                <?php foreach ($tags as $tag): ?>
                    <span class="article-detail__tag"><?= htmlspecialchars($tag->name) ?></span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </article>

    <!-- ============================================================
         KOMENTÁŘE
         ============================================================ -->
    <section class="comments">
        <h2>Komentáře (<?= count($comments) ?>)</h2>

        <?php if (isset($_GET['posted'])): ?>
            <p class="comments__notice">
                Děkujeme! Komentář byl odeslán a čeká na schválení.
            </p>
        <?php endif; ?>

        <?php if ($comments === []): ?>
            <p>Zatím tu nejsou žádné komentáře. Buďte první!</p>
        <?php else: ?>
            <ul class="comments__list">
                <?php foreach ($comments as $comment): ?>
                    <li class="comment">
                        <header class="comment__header">
                            <strong><?= htmlspecialchars($comment->authorName) ?></strong>
                            <time><?= htmlspecialchars($comment->getFormattedCreatedAt()) ?></time>
                        </header>
                        <p><?= nl2br(htmlspecialchars($comment->content)) ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <!-- Formulář pro nový komentář -->
        <form method="post" class="comment-form">
            <h3>Přidat komentář</h3>
            <input type="hidden" name="article_id" value="<?= $article->id ?>">

            <div class="form-row">
                <label for="comment-name">Jméno *</label>
                <input
                    type="text" id="comment-name" name="name"
                    value="<?= htmlspecialchars($commentFormData['name']) ?>"
                    class="<?= isset($commentErrors['name']) ? 'input--error' : '' ?>"
                    required
                >
                <?php if (isset($commentErrors['name'])): ?>
                    <span class="form-error"><?= htmlspecialchars($commentErrors['name']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-row">
                <label for="comment-email">E-mail *</label>
                <input
                    type="email" id="comment-email" name="email"
                    value="<?= htmlspecialchars($commentFormData['email']) ?>"
                    class="<?= isset($commentErrors['email']) ? 'input--error' : '' ?>"
                    required
                >
                <?php if (isset($commentErrors['email'])): ?>
                    <span class="form-error"><?= htmlspecialchars($commentErrors['email']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-row">
                <label for="comment-content">Komentář *</label>
                <textarea
                    id="comment-content" name="content" rows="4"
                    class="<?= isset($commentErrors['content']) ? 'input--error' : '' ?>"
                    required
                ><?= htmlspecialchars($commentFormData['content']) ?></textarea>
                <?php if (isset($commentErrors['content'])): ?>
                    <span class="form-error"><?= htmlspecialchars($commentErrors['content']) ?></span>
                <?php endif; ?>
            </div>

            <button type="submit" name="submit_comment" class="btn">Odeslat komentář</button>
            <p class="form-note">
                Komentáře jsou před zveřejněním schvalovány administrátorem.
            </p>
        </form>
    </section>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>
