<?php foreach ($articles as $article): ?>
	<div id="article<?= $article->getId() ?>" class="article__short">
	    <h2><a href="/articles/<?= $article->getId() ?>" class="article__name"><?= $article->getName() ?></a></h2>
	    <p><?= $article->getParsedText() ?></p>
    </div>
<?php endforeach; ?>
<script src="/js/load-list-articles.js"></script>