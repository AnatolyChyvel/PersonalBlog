<?php include __DIR__ . '/../header.php'; ?>
<h1>Редактирование комментария</h1><br>
<?php if(!empty($error)): ?>
    <div style="background-color: red"><?= $error ?></div>
<?php endif; ?>
<form action="/comments/<?= $comment->getId() ?>/edit" method="post">
    <label for="comment>">Текст комментария</label><br>
    <textarea class="article__input" name="comment" id="comment" rows="8" cols="100"><?= $_POST['comment'] ?? $comment->getText() ?></textarea><br>
    <br>
    <input class="log_btn" type="submit" value="Обновить">
</form>
<?php include __DIR__ . '/../footer.php' ?>
