<?php include __DIR__ . '/../header.php'; ?>
<div class="article__view">
	    <h1><?= $article->getName() ?></h1>
	    <p><?= $article->getParsedtext() ?></p>
	    <p class="author__article">Автор: <a href="/users/<?= $article->getAuthor()->getId() ?>/profile"><?= $article->getAuthor()->getNickname() ?></a></p>
	    <?php include __DIR__ . '/../articles/btnEditAndDelete.php'?>
    </div>
    <div class="comments__for__article">
<?php include __DIR__ . '/../comments/add.php' ?>
    <br>
<?php include __DIR__ . '/../comments/view.php' ?>
<?php include __DIR__ . '/../footer.php' ?>
</div>