<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/styles.css">
    <title>Title</title>
</head>

<body>

<nav>
    <div>
        <a href="/" id="logo">
            <img src="/img/logo.jpg" alt="logo" title="Antisocial Network">
            <p><span>Anti</span>social network</p>
        </a>
    </div>
    <div>
    <?php if ($user): ?>
        <a href="/profile"><?= $user->username ?></a>
        <a href="/logout">logout</a>
    <?php else: ?>
        <a href="/login">login</a>
        <a href="/signup">signup</a>
    <?php endif; ?>
    </div>
</nav>

<div class="container">
    <?php if (isset($messages)): ?>
        <?php foreach ($messages as $message): ?>
            <div class="alert alert-<?= $message['type'] ?>">
                <?= $message['text'] ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
