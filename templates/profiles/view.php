<?php include __DIR__ . '/../header.php' ?>
    <div class="profile">
	    <div class="ava">
	    <h1>Профиль</h1>
	    <img src="data:image/jpg;base64,<?= $profile->getSquareImage() ?>" width="200"><br>
	</div>
	<div class="profile__data">
	    Имя пользователя: <?= $owner->getNickname() ?><br>
	    <p>Последняя активность:</p><p class="datetime"><?= $profile->getLastActivity() ?><p><br>
	    О себе: <?= $profile->getAboutMe() ?? '' ?><br>
		<?php include __DIR__ . '/../admins/editProfile.php' ?>
		</div>
    </div>
    <div class="articles__profile">
    <h2>Статьи от данного пользователя:</h2>
<?php if (!empty($articles)): ?>
    <?php include __DIR__ . '/../articles/listOfArticles.php' ?>
<?php else: ?>
    <?= 'У данного пользователей нет статей.' ?>
<?php endif; ?>
<script src="/js/transform-datetime.js"></script>
</div>
<?php include __DIR__ . '/../footer.php' ?>