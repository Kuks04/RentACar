<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Rent A Car - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../../styles/style.css">
</head>

<body class="bg-dark text-white">
    <?php
    $email = null;

    // provera da li je forma podneta
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // dodeljivanje vrednosti promenljivama
        $email = $_POST['email'];
        $pass = $_POST['pass'];
        $remember = isset($_POST['remember']) ? 1 : 0;

        // provera da li su sva polja popunjena
        if (!empty($_POST['email']) && !empty($_POST['pass'])) {
            // provera da li je email u ispravnom formatu
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'The email is not in the correct format.';
            } else $errors['email'] = null;

            // provera da li lozinka ima manje od 8 karaktera
            if (strlen($pass) < 8) {
                $errors['pass'] = 'Password must have more than 8 characters.';
            } else $errors['pass'] = null;

            // ako nema gresaka mozemo proveriti da li ima naloga u bazi podataka
            if ($errors['email'] == null && $errors['pass'] == null) {
                include '../../sql/connection.php';
                $select = "SELECT * FROM korisnik WHERE email = '" . $email . "'";
                $check = mysqli_query($conn, $select);

                // proveravamo da li postoji uneti email
                if (mysqli_num_rows($check) < 1) {
                    $errors['email'] = "The email address doesn't exists, try another one.";
                } else {
                    $value = mysqli_fetch_assoc($check);
                    // proveravamo da li je uneta lozinka tacna
                    if ($pass == $value['lozinka']) {
                        // ubacujemo podatke u sesije
                        $_SESSION['id'] = $value['id'];
                        $_SESSION['fname'] = $value['ime'];
                        $_SESSION['lname'] = $value['prezime'];
                        $_SESSION['email'] = $email;
                        $_SESSION['pass'] = $pass;
                        $_SESSION['img'] = $value['slika'];

                        // proveramo da li se admin ulogovao da bi mogao da ima napredne opcije
                        if ($value['id_korisnika'] == 2) {
                            $_SESSION['admin'] = true;
                        }

                        // ako je checkbox cekiran, pravimo cookie da zapamtimo podatke  
                        if ($remember == 1) {
                            // 7200 = 2 * 60 * 60 = 2 sata - Cookie se cuvaju narednih 2 sata
                            setcookie('id', $_SESSION['id'], time() + 7200, "/");
                            setcookie('fname', $_SESSION['fname'], time() + 7200, "/");
                            setcookie('lname', $_SESSION['lname'], time() + 7200, "/");
                            setcookie('email', $_SESSION['email'], time() + 7200, "/");
                            setcookie('pass', $_SESSION['pass'], time() + 7200, "/");
                            setcookie('img', $_SESSION['img'], time() + 7200, "/");
                            if (isset($_SESSION['admin'])) {
                                setcookie('admin', $_SESSION['admin'], time() + 7200, "/");
                            }
                        } 
                        
                        // Ako je pokusao da rezervise kola, a nije bio ulogovan
                        if(!isset($_SESSION['carid'])) {
                            echo "<script>window.location = '../../index.php';</script>"; // povratak na index.php  
                        }
                        else {
                            $carid = $_SESSION['carid'];
                            unset($_SESSION['carid']); // Brisemo sesiju, da na sledecem loginu nas ne bi poslalo na rezervacije
                            echo "<script> window.location = '../user/reservations.php?id=" . $carid . "'; </script>";
                        }
                       
                    } else $errors['pass'] = "Wrong password, try again.";
                }
            }
        } else {
            $errors['empty'] = 'Please fill in all fields.';
        }
    }

    ?>

    <section class="login">
        <div class="px-4 py-5 px-md-5 text-center text-lg-start">
            <div class="container">
                <a class="navbar-brand text-white lead m-3" style="font-size: 250%;" href="../../index.php">RentA<span class="text-primary">Car</span></a>
                <div class="row gx-lg-5">
                    <div class="col-lg-6 mb-5 mb-lg-0">
                        <h1 class="my-5 display-3 fw-bold ls-tight">
                            Life is too short <br />
                            <span class="text-primary">to take the buss</span>
                        </h1>
                        <p style="color: hsl(217, 10%, 50.8%)">
                            Lorem ipsum dolor sit amet consectetur adipisicing elit.
                            Eveniet, itaque accusantium odio, soluta, corrupti aliquam
                            quibusdam tempora at cupiditate quis eum maiores libero
                            veritatis? Dicta facilis sint aliquid ipsum atque?
                        </p>
                    </div>
                    <div class="col-lg-6 mb-5 mb-lg-0">
                        <div class="card border-0">
                            <div class="card-body py-5 px-md-5 bg-dark">
                                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                    <h1 class="mb-3 display-5 fw-bold">
                                        LOGIN
                                    </h1>

                                    <!-- Email input -->
                                    <div class="form-outline mb-4">
                                        <label class="form-label" for="form3Example3">Email address</label>
                                        <input type="text" id="form3Example3" name="email" value="<?php echo $email; ?>"
                                            class="form-control <?php if (!empty($errors['email'])) { echo 'is-invalid'; }?>" />
                                        <?php
                                        if (!empty($errors['email'])) {
                                            echo '<p class="text-danger mt-3">' . $errors['email'] . '</p>';
                                        }
                                        ?>
                                    </div>

                                    <!-- Password input -->
                                    <div class="form-outline mb-2">
                                        <label class="form-label" for="form3Example4">Password</label>
                                        <input type="password" id="form3Example4" name="pass"
                                            class="form-control <?php if (!empty($errors['pass'])) { echo 'is-invalid'; }?>" />
                                        <?php
                                        if (!empty($errors['pass'])) {
                                            echo '<p class="text-danger mt-3">' . $errors['pass'] . '</p>';
                                        }
                                        ?>
                                    </div>

                                    <!-- Zaboravljena sifra -->
                                    <div class="d-flex mb-2">
                                        <a href="./forgotpass.php" class="text-decoration-none">Forgot password?</a>
                                    </div>

                                    <!-- Checkbox da se zapamti login -->
                                    <div class="form-check d-flex mb-4">
                                        <input class="form-check-input me-2" type="checkbox" value="" id="form2Example33" name="remember" />
                                        <label class="form-check-label" for="form2Example33">
                                            Remember me
                                        </label>
                                    </div>

                                    <!-- Login button -->
                                    <button type="submit" class="btn btn-primary btn-block px-3 py-2 mb-4" name="login">
                                        Login
                                    </button>

                                    <!-- Registracija -->
                                    <div class="d-flex mb-4">
                                        <p>Don't have an account? <a href="./register.php" class="text-decoration-none">Register!</a>
                                    </div>

                                    <?php
                                    if (!empty($errors['empty'])) {
                                        echo '<p class="text-danger mt-3">' . $errors['empty'] . '</p>';
                                    }
                                    ?>
                                </form>
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