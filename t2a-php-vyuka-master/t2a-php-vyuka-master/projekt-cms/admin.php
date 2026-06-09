<?php

declare(strict_types=1);

/**
 * UKÁZKOVÁ STRÁNKA – administrátorská nástěnka
 *
 * Co tato stránka ukazuje:
 *   - Kontrola přihlášení (Auth::requireLogin přesměruje, pokud nikdo nepřihlášen)
 *   - Zobrazení přehledu: všechny články + čekající komentáře
 *   - Akce pro admina – schválit / smazat komentář
 *
 * Skutečnou administraci (CRUD pro články atd.) si student domyslí
 * podle vzoru – tato stránka slouží jen jako ukázka princip + použití repozitářů.
 */

require_once __DIR__ . '/src/bootstrap.php';

$auth = new Auth();
$auth->requireLogin(); // pokud nikdo nepřihlášen, přesměruje na admin-login.php

$articleRepo = new ArticleRepository();
$commentRepo = new CommentRepository();

// Zpracování akcí na komentářích (schválit / smazat)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_action'], $_POST['comment_id'])) {
	$commentId = (int) $_POST['comment_id'];
	$action = $_POST['comment_action'];

	if ($action === 'approve') {
		$commentRepo->approve($commentId);
	} elseif ($action === 'delete') {
		$commentRepo->delete($commentId);
	}

	header('Location: admin.php');
	exit;
}

$allArticles = $articleRepo->getAll();
$pendingComments = $commentRepo->getPending();

$pageTitle = 'Administrace – CMS';
$currentUser = $auth->getCurrentUser();

?>
<?php require __DIR__ . '/partials/header.php'; ?>

<main class="container">
    <h1>Administrace</h1>
    <p>Vítej, <strong><?= htmlspecialchars($currentUser->name) ?></strong>.</p>

    <!-- ============================================================
         PŘEHLED ČLÁNKŮ
         ============================================================ -->
    <section>
        <h2>Všechny články (<?= count($allArticles) ?>)</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Titulek</th>
                    <th>Kategorie</th>
                    <th>Stav</th>
                    <th>Publikováno</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($allArticles as $article): ?>
                    <tr>
                        <td>
                            <a href="clanek.php?slug=<?= htmlspecialchars($article->slug) ?>">
                                <?= htmlspecialchars($article->title) ?>
                            </a>
                        </td>
                        <td><?= htmlspecialchars($article->categoryName ?? '—') ?></td>
                        <td>
                            <span class="status status--<?= htmlspecialchars($article->status) ?>">
                                <?= $article->isPublished() ? 'Publikováno' : 'Koncept' ?>
                            </span>
                        </td>
                        <td>
                            <?= $article->publishedAt !== NULL
                                ? htmlspecialchars($article->getFormattedPublishedAt())
                                : '—' ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>

    <!-- ============================================================
         ČEKAJÍCÍ KOMENTÁŘE
         ============================================================ -->
    <section>
        <h2>Komentáře ke schválení (<?= count($pendingComments) ?>)</h2>

        <?php if ($pendingComments === []): ?>
            <p>Žádné komentáře nečekají na schválení.</p>
        <?php else: ?>
            <ul class="admin-comments">
                <?php foreach ($pendingComments as $comment): ?>
                    <li class="admin-comments__item">
                        <header>
                            <strong><?= htmlspecialchars($comment->authorName) ?></strong>
                            &lt;<?= htmlspecialchars($comment->authorEmail) ?>&gt;
                            <time><?= htmlspecialchars($comment->getFormattedCreatedAt()) ?></time>
                        </header>
                        <p><?= nl2br(htmlspecialchars($comment->content)) ?></p>
                        <form method="post" class="admin-comments__actions">
                            <input type="hidden" name="comment_id" value="<?= $comment->id ?>">
                            <button type="submit" name="comment_action" value="approve" class="btn btn--small">
                                Schválit
                            </button>
                            <button type="submit" name="comment_action" value="delete" class="btn btn--small btn--danger">
                                Smazat
                            </button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>
