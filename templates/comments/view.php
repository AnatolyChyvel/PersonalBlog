<?php if (isset($comments)): ?>
    <?php foreach ($comments as $comment): ?>
        <div id="comment<?= $comment->getId() ?>" class="comment__view">
            <div class="ava__nick">
                <img src="data:image/jpg;base64,<?= $comment->getAuthorProfile()->getSquareImage()?>" width="100"><br>
                <a href="/users/<?= $comment->getAuthor()->getId() ?>/profile"><?= $comment->getAuthor()->getNickname() ?></a>
            </div>
            <div class="comment__data">
                <p class="datetime"><?= $comment->getCreatedAt() ?></p>
                <br>
                <p id="commentText<?= $comment->getId() ?>" class="comment__text"><?= $comment->getText() ?></p>
                <?php if (isset($user)): ?>
                    <?php if ($user->isAdmin() || $user->getId() === $comment->getAuthor()->getId()): ?>
                        <a href="/comments/<?= $comment->getId() ?>/edit">Редактировать</a>
                        <a href="/comments/<?= $comment->getId() ?>/delete">Удалить</a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
    <script src="/js/transform-datetime.js"></script>
    <script src="/js/load-comments.js"></script>
<?php endif; ?>