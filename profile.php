<?php include('inc/header.php');
korisnicka_restrikcija();
 ?>
<div>
    <?php display_message(); ?>
</div>

<?php 
    $user = get_user();
    echo "<img class='profilna_slika' src='" .$user['profilna_slika'] . "'>";

    user_profile_image_upload();
?>

<form method="POST" enctype="multipart/form-data">
    Izaberi fotografiju 
    <input type="file" name="profilna_slika_fajl">
    <input type="submit" value="Objavi fotografiju" name="potvrdi">
</form>

<?php include('inc/footer.php'); ?>