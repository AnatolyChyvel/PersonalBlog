<?php include __DIR__ . '/../header.php'; ?>
<div class="article__create">
	<h1>Создание новой статьи</h1><br>
	<?php if(!empty($error)): ?>
	    <div style="background-color: red"><?= $error ?></div>
	<?php endif; ?>
	<form action="/articles/add" method="post">
	    <label for="name">Название статьи</label><br>
	    <input class="article__input" type="text" name="name" id="name" value="<? $_POST['name'] ?? '' ?>" size="50"><br>
	    <br>
	    <label for="text>">Текст статьи</label><br>
	    <textarea class="article__input" name="text" id="text" rows="18" cols="100"><? $_POST['text'] ?? '' ?></textarea><br>
	    <br>
	    <input type="submit" value="Создать" class="article__btn">
	</form>
</div>
<?php include __DIR__ . '/../footer.php' ?>
