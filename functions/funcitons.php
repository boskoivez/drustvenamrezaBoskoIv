<?php
function clean($string) {
    return htmlentities($string);
}

function redirect($location){
    header(header: "location: {$location}");
    exit();

}

function set_message($message){
    if(!empty($message)) {
        $_SESSION['message'] = $message;
    } 
    else {
        $message = "";
    }
}

function display_message(){
    if(isset($_SESSION['message'])) {
        echo $_SESSION['message'];
        unset($_SESSION['message']);
    }
}

function email_exists($email){
    $email = filter_var($email, filter:FILTER_SANITIZE_EMAIL);
    $query = "SELECT id FROM users WHERE email = '$email'";
    $result = query($query);

    if($result->num_rows > 0) {
        return true;
    }  
        return false;
    
}

function user_exists($korisnik){
    $korisnik = filter_var($korisnik, filter:FILTER_SANITIZE_STRING);
    $query = "SELECT id FROM users WHERE email = '$korisnik'";
    $result = query($query);

    if($result->num_rows > 0) {
        return true;
    } 
        return false;
}

function validation_user_reqistration(){
    $errors = [];
    if($_SERVER['REQUEST_METHOD'] == "POST"){
        $ime = clean($_POST['ime']);
        $prezime = clean($_POST['prezime']);
        $korisnicko_ime = clean($_POST['korisnicko_ime']);
        $email = clean($_POST['email']);
        $sifra = clean($_POST['sifra']);
        $potvrda_sifre = clean($_POST['potvrda_sifre']);

        if(strlen($ime) < 3 ) {
            $errors[] = "Ime ne sme da sadrži manje od tri karaktera.";
        }
        if(strlen($prezime) < 3 ) {
            $errors[] = "Prezime ne sme da sadrži manje od tri karaktera.";
        }
        if(strlen($korisnicko_ime) < 3 ) {
            $errors[] = "Korisničko ime ne sme da sadrži manje od tri karaktera.";
        }
        if(strlen($korisnicko_ime) > 15 ) {
            $errors[] = "Korisničko ime ne sme da sadrži više od 15 karaktera.";
        }
        if(email_exists($email)) {
            $errors[] = "Uneti email je već u sistemu";
        }
        if(user_exists($korisnicko_ime)){
            $errors[] = "Uneto korisničko ime je već u sistemu";
        }
        if(strlen($sifra) < 8) {
            $errors[] = "Šifra ne sme da sadrži manje od 8 karaktera";
        }
        if($sifra != $potvrda_sifre) {
            $errors[] = "Unete šifre se ne podudaraju";
        }
        if(!empty($errors)){
            foreach($errors as $error){
                echo "<div class='alert'>. $error .</div>";
            } 
        } else {
            $ime = filter_var($ime, filter:FILTER_SANITIZE_STRING);
            $prezime = filter_var($prezime, filter:FILTER_SANITIZE_STRING);
            $korisnicko_ime = filter_var($korisnicko_ime, filter:FILTER_SANITIZE_STRING);
            $email = filter_var($email, filter:FILTER_SANITIZE_EMAIL);
            $sifra = filter_var($sifra, filter:FILTER_SANITIZE_STRING);
            create_user($ime,$prezime,$korisnicko_ime,$email,$sifra);
        }
        
    }

}

function create_user($ime,$prezime,$korisnicko_ime,$email,$sifra) {

        $ime = escape($ime);
        $prezime = escape($prezime);
        $korisnicko_ime = escape($korisnicko_ime);
        $email = escape($email);
        $sifra = escape($sifra);
        $sifra = password_hash($sifra, algo:PASSWORD_DEFAULT);

        $sql = "INSERT INTO users(ime, prezime, korisnicko_ime, profilna_slika, email, sifra)";
        $sql .= "VALUES('$ime','$prezime','$korisnicko_ime','uploads/default.jpg', '$email', '$sifra')";

        confim(query($sql));
        set_message("Uspeštno ste se registrovali i kreirali nalog.");
        redirect("login.php");
}

function validate_user_login()
{
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $email = clean($_POST['email']);
        $sifra = clean($_POST['sifra']);

        if (empty($email)) {
            $errors[] = "Email ne može biti prazan";
        }
        if (empty($sifra)) {
            $errors[] = "Šifra ne može biti prazna";
        }
        if (empty($errors)) {
            if (user_login($email, $sifra)) {
                redirect('index.php');
            } else {
                $errors[] = "Email ili šifra se ne podudaraju. Pokušaj opet";
            }
        }
        if (!empty($errors)) {
            foreach ($errors as $error) {
                echo '<div class="alert">' . $error . '</div>';
            }
        }
    }

}

function user_login($email, $sifra)
{
    $password = filter_var($sifra, FILTER_SANITIZE_STRING);
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);

    $query = "SELECT * FROM users WHERE email='$email'";
    $result = query($query);

    if ($result->num_rows == 1) {
        $data = $result->fetch_assoc();

        if (password_verify($sifra, $data['sifra'])) {
            $_SESSION['email'] = $email;
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function get_user($id = NULL) {
    if($id != NULL) {
        $query = "SELECT * FROM users WHERE id=" . $id;
        $result = query($query);

        if($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return "Korisnik nije pronađen";
        }
    } else {
        $query = "SELECT * FROM users WHERE email='" .$_SESSION['email'] ."'";
        $result = query($query);

        if($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return "Korisnik nije pronađen";
        }
    }
}


function user_profile_image_upload()
{
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $target_dir = "uploads/";
        $user = get_user();
        $user_id = $user['id'];
        $target_file = $target_dir . $user_id . "." .pathinfo(basename($_FILES["profilna_slika_fajl"]["name"]), PATHINFO_EXTENSION);;
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $error = "";

        $check = getimagesize($_FILES["profilna_slika_fajl"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            $error = "Fajl nije fotografija.";
            $uploadOk = 0;
        }

        if ($_FILES["profilna_slika_fajl"]["size"] > 5000000) {
            $error = "Oh, fajl je prevelik (u MB).";
            $uploadOk = 0;
        }

        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            $error = "Oh, podržane ekstenzije su: JPG, JPEG, PNG i GIF .";
            $uploadOk = 0;
        }

        if ($uploadOk == 0) {
            set_message('Greška prilikom objave: '. $error);
        } else {
            $sql = "UPDATE users SET profilna_slika='$target_file' WHERE id=$user_id";
            confim(query($sql));
            set_message('Profilna slika je uspešno postavljena!');

            if (!move_uploaded_file($_FILES["profilna_slika_fajl"]["tmp_name"], $target_file)) {
                set_message('Greška prikikom objave: '. $error);
            }
        }

        redirect('profile.php');
    }
}


function korisnicka_restrikcija() {
    if(!isset($_SESSION['email'])){
        redirect(location: "login.php");
    }
}

function login_check_pages() {
    if(isset($_SESSION['email'])) {
        redirect(location:"index.php");
    }
}


function create_post() {
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $post_sadrzaj = clean($_POST['post_sadrzaj']);

        if(strlen($post_sadrzaj) > 200) {
            $errors[] = "Tvoj sadržaj je predugačak. Smanji karaktere";
        }

        if(!empty($errors)) {
            foreach ($errors as $error) {
                echo '<div class="alert">' . $error . '</div>';
            }
        } else {
            $post_sadrzaj = filter_var($post_sadrzaj, filter:FILTER_SANITIZE_STRING);
            $post_sadrzaj = escape($post_sadrzaj);

            $user = get_user();
            $korisnicki_id = $user['id'];

            $sql = "INSERT INTO objave(korisnicki_id, sadrzaj, svidjanja)";
            $sql .= "VALUES ($korisnicki_id, '$post_sadrzaj', 0)";

            confim(query($sql));
            set_message('Upravo ste dodali objavu');
            redirect(location: "index.php");
        }
    }
}

function fetch_all_posts() {
    $query = "SELECT * FROM objave ORDER BY vreme_kreiranja DESC";
    $result = query($query);

    if($result->num_rows > 0) {
        while($row = $result->fetch_assoc()){
            $user = get_user($row['korisnicki_id']);

            echo "<div class='post'>
            
                <p><img src='" . $user['profilna_slika'] . "'><b>" . $user['ime'] . " " . $user['prezime'] . "<b></p>
                <p>" . $row['sadrzaj'] ."</p>
                <h4><i>Vreme objave:" . $row['vreme_kreiranja'] . "</i></h4>
                
                </div>";
            
                
        }
    }
}