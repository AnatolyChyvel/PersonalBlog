<?php if ($user !== null && $user->isAdmin()): ?>
    <a href="<?= $article->getId() ?>/edit">Редактировать</a>
    <a style="float: right" href="<?= $article->getId() ?>/delete">Удалить</a>
<?php endif; ?>