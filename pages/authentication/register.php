<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Rent A Car - Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../../styles/style.css">
</head>

<body class="bg-dark text-white">
    <?php
    $fname = $lname = $email = null;

    // provera da li je forma podneta
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // dodeljivanje vrednosti promenljivama
        $fname = $_POST['fname'];
        $lname = $_POST['lname'];
        $email = $_POST['email'];
        $pass = $_POST['pass'];
        $cpass = $_POST['cpass'];
        $news = isset($_POST['news']) ? 1 : 0;

        // provera da li su sva polja popunjena
        if (!empty($_POST['fname']) && !empty($_POST['lname']) && !empty($_POST['email']) && !empty($_POST['pass']) && !empty($_POST['cpass'])) {
            // provera da li je ime sastavljeno samo od slova
            if (!preg_match('/^[a-zA-Z]{2,30}$/', $fname)) {
                $errors['fname'] = 'The name must contain only letters.';
            } else $errors['fname'] = null;

            // provera da li je prezime sastavljeno samo od slova
            if (!preg_match('/^[a-zA-Z]{2,30}$/', $lname)) {
                $errors['lname'] = 'The last name must contain letters.';
            } else $errors['lname'] = null;

            // provera da li je email u ispravnom formatu
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'The email is not in the correct format.';
            } else $errors['email'] = null;

            // provera da li lozinka ima manje od 8 karaktera
            if (strlen($pass) < 8) {
                $errors['pass'] = 'Password must have more than 8 characters.';
            } else $errors['pass'] = null;

            // provera da li su lozinke jednake
            if ($pass != $cpass) {
                $errors['cpass'] = 'Passwords do not match.';
            } else $errors['cpass'] = null;

            // ako nema gresaka mozemo sacuvati podatke u bazi podataka
            if ($errors['fname'] == null && $errors['lname'] == null && $errors['email'] == null && $errors['pass'] == null && $errors['cpass'] == null) {
                include '../../sql/connection.php';
                $select = "SELECT * FROM korisnik WHERE email = '" . $email . "'";
                $insert = "INSERT INTO korisnik(ime, prezime, email, lozinka) 
                VALUES('$fname','$lname','$email','$pass')";
                $check = mysqli_query($conn, $select);

                // proveravamo da li se email vec nalazi u bazi podataka
                if (mysqli_num_rows($check) > 0) {
                    $errors['email'] = "The email address already exists, try another one.";
                } else if (mysqli_query($conn, $insert)) {
                    // ako je checkbox cekiran, email se ubacuje u bazu za slanje mailova
                    if ($news == 1) {
                        $newsletter = "INSERT INTO newsletter(ime, prezime, email) 
                        VALUES('$fname','$lname','$email')";
                        mysqli_query($conn, $newsletter);
                    }
                    // povratak na index.php
                    echo "<script>alert('You have registered successfully! You will be headed to login page!');
                        window.location = './login.php';</script>";
                }
            }
        } else {
            $errors['empty'] = 'Please fill in all fields.';
        }
    }

    ?>

    <section class="register">
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
                                        REGISTER
                                    </h1>
                                    <!-- 2 column grid layout sa tekst inputima za ime i prezime -->
                                    <div class="row">
                                        <div class="col-md-6 mb-4">
                                            <div class="form-outline">
                                                <label class="form-label" for="form3Example1">First name</label>
                                                <input type="text" id="form3Example1" name="fname" value="<?php echo $fname; ?>"
                                                    class="form-control <?php if (!empty($errors['fname'])) { echo 'is-invalid'; }?>" />
                                                <?php
                                                if (!empty($errors['fname'])) {
                                                    echo '<p class="text-danger mt-3">' . $errors['fname'] . '</p>';
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <div class="form-outline">
                                                <label class="form-label" for="form3Example2">Last name</label>
                                                <input type="text" id="form3Example2" name="lname" value="<?php echo $lname; ?>" 
                                                    class="form-control <?php if (!empty($errors['lname'])) { echo 'is-invalid'; }?>" />
                                                <?php
                                                if (!empty($errors['lname'])) {
                                                    echo '<p class="text-danger mt-3">' . $errors['lname'] . '</p>';
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>

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
                                    <div class="form-outline mb-4">
                                        <label class="form-label" for="form3Example4">Password</label>
                                        <input type="password" id="form3Example4" name="pass" 
                                            class="form-control <?php if (!empty($errors['pass'])) { echo 'is-invalid'; }?>" />
                                        <?php
                                        if (!empty($errors['pass'])) {
                                            echo '<p class="text-danger mt-3">' . $errors['pass'] . '</p>';
                                        }
                                        ?>
                                    </div>

                                    <!-- Confirm password input -->
                                    <div class="form-outline mb-4">
                                        <label class="form-label" for="form3Example5">Confirm Password</label>
                                        <input type="password" id="form3Example5" name="cpass"
                                            class="form-control <?php if (!empty($errors['cpass'])) { echo 'is-invalid'; }?>" />
                                        <?php
                                        if (!empty($errors['cpass'])) {
                                            echo '<p class="text-danger mt-3">' . $errors['cpass'] . '</p>';
                                        }
                                        ?>
                                    </div>

                                    <!-- Checkbox za newsletter -->
                                    <div class="form-check d-flex mb-4">
                                        <input class="form-check-input me-2" type="checkbox" value="" id="form2Example33" name="news" />
                                        <label class="form-check-label" for="form2Example33">
                                            Subscribe to our newsletter
                                        </label>
                                    </div>

                                    <!-- Register button -->
                                    <button type="submit" class="btn btn-primary btn-block px-3 py-2" name="register">
                                        Register
                                    </button>

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