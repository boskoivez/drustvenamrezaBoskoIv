<?php include "inc/header.php";
login_check_pages();
?>
<div>
<?php
  validation_user_reqistration();
  display_message();
?>
</div>

    <form method="POST">
        <input type="text" name="ime" placeholder="Ime" required>
        <input type="text" name="prezime" placeholder="Prezime" required>
        <input type="text" name="korisnicko_ime" placeholder="Korisničko ime" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="sifra" placeholder="Šifra" required>
        <input type="password" name="potvrda_sifre" placeholder="Potvrda šifre" required>
        <input type="submit" name="potvrda-registracije" value="Registruj se">


    </form>

<?php include "inc/footer.php"; ?>

