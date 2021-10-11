<?php include "inc/header.php" ?>


<?php 
if(isset($_SESSION['email'])) : ?>

<?php create_post(); ?>
<br>


<form method="POST">
    <h3>Kreiraj objavu</h3>
    <textarea name="post_sadrzaj"  cols="40" rows="8" placeholder="Napiši o čemu razmišljaš ?"></textarea>
    <input type="submit" value="Objavi" name="submit">
</form>

<div>
    <?php display_message(); ?>
</div>

<hr>

<div class="posts">

<?php  fetch_all_posts(); ?>

</div>


<?php else : ?>

<div class="homepage">
    <h1>DOBRO DOŠLI NA BI DRUŠTVENU MREŽU</h1>
    <p>Boško, tj kreator BI društvene mreže želi ti veliki pozdrav</p>

    <h2>Prijavi se <a href="login.php">ovde</a> da postaneš član BI-a</h2>
    <img src="/" alt="">
</div>

<?php endif; ?>

<?php include "inc/footer.php"; 