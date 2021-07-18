<?php if (isset($user) && $user->isAdmin()): ?>
    <li><a href="/admins/comments">Комментарии</a></li>
    <li><a href="/articles/add">Написать статью</a></li>
<?php endif; ?>