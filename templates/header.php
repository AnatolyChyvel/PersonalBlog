<!DOCTYPE html>
<html lang="ru" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Персональный блог</title>
    <link rel="stylesheet" href="/css/styles.css">
  </head>
  <body>
    <header id="header" class="header">
      <div class="container">
        <div class="nav">
              <h2 class="logo">Personal Blog</h2>
              <ul class="menu">
                <li><a href="/">Главная</a></li>
                <?php if (isset($user)): ?>
                    <li><a href="/users/<?= $user->getId() ?>/profile">Профиль</a></li>
                <?php endif; ?>
                <?php include __DIR__ . '/admins/footerForAdmin.php' ?>
              </ul>
              <div class="sign">
                <?php if (!empty($user)): ?>
                    <img src="/img/user_logo.png">
                    <h3><?= $user->getNickname() ?></h3>
                    <a href="/users/logout" class="logout">Выйти</a>
                <?php else: ?>
                    <a href="/users/login" class="btn">Войти</a>
                    <a href="/users/register">Регистрация</a>
                <?php endif; ?>
              </div>
        </div>
      </div>
    </header>
    <section id="content" class="content">
      <div class="container">
