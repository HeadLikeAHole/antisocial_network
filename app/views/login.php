<?php include 'header.php'; ?>

    <h1>Log into Antisocial Network</h1>

    <form method="post">

        <?php $form->render() ?>

        <input type="submit" value="Log in">

    </form>

    <a href="/password-reset-request">Forgot password?</a>

<?php include 'footer.php'; ?>
