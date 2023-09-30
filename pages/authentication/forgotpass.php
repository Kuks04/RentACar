<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Rent A Car - Forgotten Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../../styles/style.css">
</head>

<body class="bg-dark text-white">
    <?php
    $email = null;

    // provera da li je forma podneta
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // dodeljivanje vrednosti promenljivoj
        $email = $_POST['email'];

        // provera da li email unet
        if (!empty($_POST['email'])) {
            // provera da li je email u ispravnom formatu
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'The email is not in the correct format.';
            } else $errors['email'] = null;

            // ako je unet ispravan email, moramo da proverimo da li postoji nalog sa tim emailom da bismo mu poslali kod na mail
            if ($errors['email'] == null) {
                include '../../sql/connection.php';
                $select = "SELECT * FROM korisnik WHERE email = '" . $email . "'";
                $check = mysqli_query($conn, $select);

                // proveravamo da li postoji uneti email
                if (mysqli_num_rows($check) > 0) {
                    $value = mysqli_fetch_assoc($check);
                    $name = $value['ime'];
                    $pass = $value['lozinka'];
                    $to = $email;
                    $subject = "Forgotten Password";
                    $_SESSION['code'] = rand(100000,999999);
                    $_SESSION['fpassemail'] = $email;
                    $message =
                    '<div style="max-width: 400px;">
                        <p>
                            Hello ' . $name . ',<br>
                            your request was to send you a code because you forgot your password.
                            If that was not you, just ignore this email. If that was you, this is 
                            your security code:
                        </p>
                        <h4 style="color:blue;">' . $_SESSION['code'] . '</h4>
                        <p>Rent A <span style="color:blue;">Car</span> Company!</p>
                    </div>';
                    $headers = "From:ognjenkuks@gmail.com \r\n";
                    $headers .= "MIME-Version: 1.0\r\n";
                    $headers .= "Content-type: text/html\r\n";

                    //saljemo email
                    if (mail($to, $subject, $message, $headers)) {
                        echo "<script>alert('Your security code has been sent!'); sessionStorage.setItem('i', 120); window.location = './entercode.php'; </script>";
                    }
                } else {
                    $errors['email'] = "Non-existent email, try another one.";
                }
            }
        } else {
            $errors['email'] = 'Please enter your email.';
        }
    }
    ?>
    
    <section class="forgotpass d-flex">
        <div class="shadow card text-center m-auto bg-dark d-flex my-auto mt-5" style="width: 400px;">
            <div class="card-header h5 bg-primary">Forgotten Password</div>
            <div class="card-body px-5">
                <p class="card-text py-2">
                    Enter your email address and we'll send you an email with your security code.
                </p>
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <label class="form-label" for="form3Example3">Email address</label>
                    <input type="text" id="form3Example3" name="email" value="<?php echo $email; ?>"
                        class="form-control <?php if (!empty($errors['email'])) { echo 'is-invalid'; }?>" />
                    <?php
                    if (!empty($errors['email'])) {
                        echo '<p class="text-danger mt-3">' . $errors['email'] . '</p>';
                    }
                    ?>
                    <button class="btn btn-primary w-100 mt-3" type="submit" name="reset">Reset password</button>
                </form>
                <div class="d-flex justify-content-between mt-4">
                    <a class="text-decoration-none" href="../../index.php">Home</a>
                    <a class="text-decoration-none" href="./login.php">Login</a>
                    <a class="text-decoration-none" href="./register.php">Register</a>
                </div>
            </div>
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
</body>

</html>