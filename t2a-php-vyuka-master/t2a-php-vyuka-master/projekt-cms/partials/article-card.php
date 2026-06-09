<?php

/**
 * PARTIAL: Karta článku
 *
 * Očekává proměnnou:
 *   $article (ArticleDTO) – článek k zobrazení
 */

?>
<article class="article-card">
    <?php if ($article->coverImage !== ''): ?>
        <a href="clanek.php?slug=<?= htmlspecialchars($article->slug) ?>">
            <img
                class="article-card__image"
                src="<?= htmlspecialchars($article->coverImage) ?>"
                alt="<?= htmlspecialchars($article->title) ?>"
            >
        </a>
    <?php endif; ?>

    <div class="article-card__body">
        <span class="article-card__category">
            <?= htmlspecialchars($article->categoryName ?? '') ?>
            <?php if ($article->publishedAt !== NULL): ?>
                · <?= htmlspecialchars($article->getFormattedPublishedAt()) ?>
            <?php endif; ?>
        </span>

        <h2 class="article-card__title">
            <a href="clanek.php?slug=<?= htmlspecialchars($article->slug) ?>">
                <?= htmlspecialchars($article->title) ?>
            </a>
        </h2>

        <p class="article-card__perex">
            <?= htmlspecialchars($article->perex) ?>
        </p>

        <?php if ($article->tags !== []): ?>
            <div class="article-card__tags">
                <?php foreach ($article->tags as $tag): ?>
                    <span class="article-card__tag"><?= htmlspecialchars($tag->name) ?></span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</article>
