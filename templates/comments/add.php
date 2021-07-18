<div class="add__comment">
    <?php if($user === null): ?>
        <p><a href="/users/login">Войдите</a> на сайт, чтобы добавлять комментарии.</p>
    <?php else: ?>
        <form action="/articles/<?= $article->getId() ?>/comments" method="post">
            <?php if(!empty($error)): ?>
                <div style="background-color: red"><?= $error ?></div>
            <?php endif; ?>
            <label for="comment">Оставить комментарий</label><br>
            <textarea class="comment__input" type="text" name="comment" id="comment" rows="7" cols="120"></textarea>
            <br>
            <input class="log_btn" type="submit" value="Добавить">
        </form>
    <?php endif; ?>
</div>