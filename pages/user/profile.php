<?php session_start(); 
$_SESSION['uploadPage']= 'profile'?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rent A Car - <?php echo $_SESSION['fname'] . " " . $_SESSION['lname']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../../styles/style.css">
</head>

<body class="bg-dark text-white">
    <?php
    $emailholder = $_SESSION['email'];
    $fname = $_SESSION['fname'];
    $lname = $_SESSION['lname'];
    $email = $_SESSION['email'];
    $pass = $_SESSION['pass'];
    $img = $_SESSION['img'];
    include '../../sql/connection.php';

    //proverava da li je korisnik vec na listi newslettera
    $select = "SELECT * FROM newsletter WHERE email = '" . $emailholder . "'";
    $check = mysqli_query($conn, $select);
    // proveravamo da li se email nalazi u newsletters
    if (mysqli_num_rows($check) > 0) {
        $newsletter = true;
        $btntext = "Unsubscribe from";
    } else {
        $newsletter = false;
        $btntext = "Subscribe to";
    }

    // provera da li je forma podneta
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save'])) {
        // provera da li su sva polja popunjena
        if (!empty($_POST['fname']) && !empty($_POST['lname']) && !empty($_POST['email'])) {
            // provera da li je ime sastavljeno samo od slova
            if (!preg_match('/^[a-zA-Z]{2,30}$/', $_POST['fname'])) {
                $errors['fname'] = 'The name must contain only letters.';
            } else {
                $fname = $_POST['fname'];
                $_SESSION['fname'] = $fname;
                $errors['fname'] = null;
            }

            // provera da li je prezime sastavljeno samo od slova
            if (!preg_match('/^[a-zA-Z]{2,30}$/', $_POST['lname'])) {
                $errors['lname'] = 'The last name must contain letters.';
            } else {
                $lname = $_POST['lname'];
                $_SESSION['lname'] = $lname;
                $errors['lname'] = null;
            }

            // provera da li je email u ispravnom formatu
            if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'The email is not in the correct format.';
            } else {
                $email = $_POST['email'];
                $_SESSION['email'] = $email;
                $errors['email'] = null;
            }

            // provera da li lozinka ima manje od 8 karaktera
            if (empty($_POST['pass'])) {
                $errors['pass'] = null;
            } else if (strlen($_POST['pass']) < 8) {
                $errors['pass'] = 'Password must have more than 8 characters.';
            } else {
                $pass = $_POST['pass'];
                $_SESSION['pass'] = $pass;
                $errors['pass'] = null;
            }

            // ako nema gresaka mozemo izmeniti podatke u bazi podataka
            if ($errors['fname'] == null && $errors['lname'] == null && $errors['email'] == null && $errors['pass'] == null) {
                $select = "SELECT * FROM korisnik WHERE email = '" . $email . "'";
                $check = mysqli_query($conn, $select);

                // proveravamo da li se email vec nalazi u bazi podataka
                if (mysqli_num_rows($check) > 0 && $email != $emailholder) {
                    $errors['email'] = "The email address already exists, try another one.";
                } else {
                    $modify = "UPDATE korisnik
                        SET ime='$fname', prezime='$lname', email='$email', lozinka='$pass'
                        WHERE email='$emailholder'";

                    if (mysqli_query($conn, $modify)) {
                        echo "<script>alert('Your profile settings have been saved.')</script>";

                        // menjamo i email na listi za newsletter ako je korisnik prijavljen
                        if ($newsletter) {
                            $modify = "UPDATE newsletter
                                SET ime='$fname', prezime='$lname', email='$email'
                                WHERE email='$emailholder'";
                            mysqli_query($conn, $modify);
                        }
                        $emailholder = $email;
                    }
                }
            }
        } else {
            $errors['empty'] = 'Please fill in all fields.';
        }
    }

    // provera da li korisnik zeli da se prijavi ili odjavi na newsletter
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['newsletter'])) {
        // ako je prijavljen onda ga izbacujemo, ako nije dodajemo
        if ($newsletter) {
            $delete = "DELETE FROM newsletter WHERE email='$emailholder'";
            $btntext = "Subscribe to";
            if (mysqli_query($conn, $delete)) {
                echo "<script>alert('You have successfully unsubscribed from the newsletter.')</script>";
            }
            $newsletter = false;
        } else {
            $select = "SELECT * FROM korisnik WHERE email = '" . $emailholder . "'";
            $value = mysqli_fetch_assoc(mysqli_query($conn, $select));
            $firstname = $value['ime'];
            $lastname = $value['prezime'];
            $insert = "INSERT INTO newsletter(ime, prezime, email) 
            VALUES('$firstname', '$lastname', '$emailholder')";
            $btntext = "Unsubscribe from";
            if (mysqli_query($conn, $insert)) {
                echo "<script>alert('You have successfully subscribed to the newsletter.')</script>";
            }
            $newsletter = true;
        }
    }
    ?>

    <section class="profile">
        <div class="container py-5">
            <div class="row">
                <div class="col">
                    <nav aria-label="breadcrumb" class="bg-dark shadow-lg rounded-3 p-3 mb-4">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="../../index.php">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Profile</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <div class="row">
                <div class="container-xl px-4 mt-4">
                    <div class="row">
                        <div class="col-xl-4">
                            <!-- Profile picture card-->
                            <div class="card bg-dark shadow-lg mb-4 mb-xl-0">
                                <div class="card-header">Profile Picture</div>
                                <div class="card-body text-center">
                                    <!-- Profile picture image-->
                                    <img class="rounded-circle mb-3" style="width: 250px; height: 250px;" src="../../images/icons/<?php echo $img; ?>" alt="Profile picture">
                                    <!-- Profile picture help block-->
                                    <div class="small font-italic text-muted mb-4">JPG, SVG or PNG no larger than 5 MB</div>
                                    <!-- Profile picture upload button-->
                                    <form id="form" action="../../utils/upload.php" method="post" enctype="multipart/form-data">
                                        <button class="btn btn-primary" onclick="document.getElementById('getFile').click();" type="button">Upload new image</button>
                                        <input type='file' id="getFile" name="upload" accept=".png, .jpg, .jpeg, .svg" style="display:none;"
                                            onchange="document.getElementById('form').submit();">
                                        <?php if (isset($_SESSION['uploaderr'])) echo '<p class="text-danger mt-3">' . $_SESSION['uploaderr'] . '</p>'; ?>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-8">
                            <!-- Account details card-->
                            <div class="card bg-dark shadow-lg mb-4">
                                <div class="card-header">Account Details</div>
                                <div class="card-body">
                                    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                        <div class="row gx-3 mb-3">
                                            <div class="col-md-6">
                                                <label class="small mb-1" for="inputFirstName">First name</label>
                                                <input name="fname" id="inputFirstName" type="text" placeholder="Enter your first name" value="<?php echo $fname; ?>" 
                                                    class="form-control <?php if (!empty($errors['fname'])) { echo 'is-invalid'; } ?>" />
                                                <?php
                                                if (!empty($errors['fname'])) {
                                                    echo '<p class="text-danger mt-3">' . $errors['fname'] . '</p>';
                                                }
                                                ?>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="small mb-1" for="inputLastName">Last name</label>
                                                <input name="lname" id="inputLastName" type="text" placeholder="Enter your last name" value="<?php echo $lname; ?>" 
                                                    class="form-control <?php if (!empty($errors['lname'])) { echo 'is-invalid'; } ?>" />
                                                <?php
                                                if (!empty($errors['lname'])) {
                                                    echo '<p class="text-danger mt-3">' . $errors['lname'] . '</p>';
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="small mb-1" for="inputEmailAddress">Email address</label>
                                            <input name="email" id="inputEmailAddress" type="text" placeholder="Enter your email address" value="<?php echo $email; ?>" 
                                                class="form-control <?php if (!empty($errors['email'])) { echo 'is-invalid'; } ?>" />
                                            <?php
                                            if (!empty($errors['email'])) {
                                                echo '<p class="text-danger mt-3">' . $errors['email'] . '</p>';
                                            }
                                            ?>
                                        </div>
                                        <div class="mb-3">
                                            <label class="small mb-1" for="inputPassword">New Password</label>
                                            <input name="pass" id="inputPassword" type="text" placeholder="Enter your new password" 
                                                class="form-control <?php if (!empty($errors['pass'])) { echo 'is-invalid';} ?>" />
                                            <?php
                                            if (!empty($errors['pass'])) {
                                                echo '<p class="text-danger mt-3">' . $errors['pass'] . '</p>';
                                            }
                                            ?>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <button class="btn btn-primary" type="submit" name="newsletter"><?php echo $btntext; ?> newsletter</button>
                                            <button class="btn btn-primary" type="submit" name="save">Save changes</button>
                                        </div>
                                    </form>
                                    <?php
                                        if (!empty($errors['empty'])) {
                                            echo '<p class="text-danger mt-3">' . $errors['empty'] . '</p>';
                                        }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
</body>

</html>