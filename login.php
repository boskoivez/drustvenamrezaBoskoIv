<?php include "inc/header.php";
login_check_pages();
?>
<div>
<?php display_message();
validate_user_login();
?>
</div>

<form method="POST">
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="sifra" placeholder="Sifra" required>
    <input type="submit" name="potvrda-prijave" value="Prijavi se"> 
</form>

<?php include "inc/footer.php"; ?>
