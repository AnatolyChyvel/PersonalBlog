<?php if (isset($user) && $user->getId() === $owner->getId()): ?>
    <a href="/users/<?= $user->getId() ?>/profile/edit">Редактировать профиль</a>
<?php endif; ?>