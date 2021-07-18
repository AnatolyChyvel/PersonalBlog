<?php include __DIR__ . '/../header.php' ?>
<?php if (isset($user) && $user->isAdmin()): ?>
    <?php include __DIR__ . '/../comments/view.php'?>
<?php endif; ?>
<?php include __DIR__ . '/../footer.php' ?>
