<?php include __DIR__ . '/../header.php'; ?>
<div class="profile__edit">
	<h1>Редактирование профиля</h1>
	<?php if (!empty($error)): ?>
	    <div style="background-color: red"><?= $error ?></div>
	<?php endif; ?>
	<form action="/users/<?= $profile->getUserId() ?>/profile/edit" method="post" enctype="multipart/form-data">
	    <label>Аватар:</label>
	    <input type="file" name="image"><br>
	    <label for="text>">О себе</label><br>
	    <textarea class="profile__input" name="aboutMe" id="aboutMe" rows="15"
	              cols="100"><?= $_POST['aboutMe'] ?? $profile->getAboutMe() ?></textarea><br>
	    <br>
	    <input class="article__btn" type="submit" value="Обновить">
	</form>
</div>
<?php include __DIR__ . '/../footer.php' ?>

