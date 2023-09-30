<?php
session_start();
// Proveravamo sa koje stranice je pozvan upload
if ($_SESSION['uploadPage'] == 'profile')
    $uploadPage = true;
else if ($_SESSION['uploadPage'] == 'addcar')
    $uploadPage = false;

include '../sql/connection.php';
// proveravamo da li je forma poslata preko HTTP POST metode
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // proveravamo da li je fajl uspesno prenesen na server
    if (is_uploaded_file($_FILES['upload']['tmp_name'])) {
        // proveravamo da li je fajl manji od 5MB
        if ($_FILES['upload']['size'] < 5242880) {
            // proveravamo da li je fajl jedan od dozvoljenih tipova
            $allowedTypes = ['image/jpeg', 'image/png', 'image/svg', 'image/jpg'];
            if (in_array($_FILES['upload']['type'], $allowedTypes)) {
                if ($uploadPage)
                    $uploadDir = "../images/icons/";
                else if (!$uploadPage)
                    $uploadDir = "../images/cars/";

                // dodajemo vreme da se ne bi desavalo da postoje slike sa istim imenima
                $fileName = time() . '_' . $_FILES['upload']['name'];
                $uploadFile = $uploadDir . basename($fileName);

                // ubacujemo fajl u nas folder
                if (move_uploaded_file($_FILES['upload']['tmp_name'], $uploadFile)) {
                    unset($_SESSION['uploaderr']);
                    if ($uploadPage) {
                        //If sluzi da se ne brise defaultna slika
                        if ($_SESSION['img'] != 'user.svg') {
                            // Kada zamenimo sliku brise se stara 
                            unlink($uploadDir . basename($_SESSION['img']));
                        }
                        $img = $_SESSION['img'] = $fileName;
                        $email = $_SESSION['email'];
                        $modify = "UPDATE korisnik
                                SET slika='$img'
                                WHERE email='$email'";
                        if (mysqli_query($conn, $modify))
                            echo "<script>alert('Image added successfully.')</script>";
                    }
                    else if(!$uploadPage) {
                        if(isset($_SESSION['fileName'])) {
                            // Kada zamenimo sliku brise se slika koja je prethodno dodata
                            unlink($uploadDir . basename($_SESSION['fileName']));
                        }
                        $_SESSION['fileName'] = $fileName;
                    }
                } else {
                    $_SESSION['uploaderr'] = 'An error occurred while uploading the file, try again.';
                }
            } else {
                $_SESSION['uploaderr'] = 'The file is not one of the allowed file types (JPEG, JPG, PNG, PDF).';
            }
        } else {
            $_SESSION['uploaderr'] = 'The file is larger than 5MB.';
        }
    } else {
        $_SESSION['uploaderr'] = 'An error occurred while transferring the file to the server, try again.';
    }
    if ($uploadPage) 
        echo "<script>window.location = '../pages/user/profile.php';</script>";
    else if(!$uploadPage)
        echo "<script>window.location = '../pages/admin/addcar.php';</script>";
}
?>