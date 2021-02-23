<?php include 'header.php'; ?>

<?php foreach ($posts as $post): ?>
    <div class="card">
        <div class="card-header">
            <?= $post['title'] ?>
        </div>
        <div class="card-media">
            <img src="<?= $post['file'] ?>" alt="file">
        </div>
        <div class="card-content">
            <?= $post['text'] ?>
            <?= $post['created'] ?>
        </div>
    </div>
<?php endforeach; ?>

<?php include 'footer.php'; ?>
