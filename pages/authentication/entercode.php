<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Rent A Car - Enter the code</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../../styles/style.css">
</head>

<body class="bg-dark text-white">
    <?php
    $code = null;

    // provera da li je forma podneta
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // dodeljivanje vrednosti promenljivoj
        $code = $_POST['code'];

        // provera da li kod unet
        if (!empty($_POST['code'])) {
            // provera da li je kod u ispravnom formatu
            if (!preg_match('/^[0-9]{6}$/', $code)) {
                $errors['code'] = 'The code is not in the correct format.';
            } else $errors['code'] = null;

            // proveravamo da li je unet ispravan kod
            if ($errors['code'] == null) {
               
                // proveravamo da li je unet ispravan kod
                if ($code == $_SESSION['code']) {    
                    include '../../sql/connection.php';
                    $select = "SELECT * FROM korisnik WHERE email = '" . $_SESSION['fpassemail'] . "'";
                    $value = mysqli_fetch_assoc(mysqli_query($conn, $select)); 
                    $_SESSION['id'] = $value['id'];
                    $_SESSION['fname'] = $value['ime'];
                    $_SESSION['lname'] = $value['prezime'];
                    $_SESSION['email'] = $value['email'];
                    $_SESSION['pass'] = $value['lozinka'];
                    $_SESSION['img'] = $value['slika'];

                    //Ako je admin
                    if ($value['id_korisnika'] == 2) {
                        $_SESSION['admin'] = true;
                    }

                    echo "<script>alert('You have logged in successfully!'); window.location = '../../index.php'; </script>";
                } else {
                    $errors['code'] = "Wrong code, try again.";
                }
            }
        } else {
            $errors['code'] = 'Please enter your code.';
        }
    }
    ?>
    
    <section class="forgotpass d-flex">
        <div class="shadow card text-center m-auto bg-dark d-flex my-auto mt-5" style="width: 400px;">
            <div class="card-header h5 bg-primary">Enter Code</div>
            <div class="card-body px-5">
                <p class="card-text py-2">
                    We've sent you a verification code to your email address <span class="text-primary"><?php echo $_SESSION['fpassemail']; ?></span>
                </p>
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <p class="card-text py-2">
                        Your code lasts: <span class="text-primary counter"></span> seconds
                    </p>
                    <label class="form-label" for="form3Example3">Your 6-digit code</label>
                    <input type="text" id="form3Example3" name="code" value="<?php echo $code; ?>"
                        class="form-control <?php if (!empty($errors['code'])) { echo 'is-invalid'; }?>" />
                    <?php
                    if (!empty($errors['code'])) {
                        echo '<p class="text-danger mt-3">' . $errors['code'] . '</p>';
                    }
                    ?>
                    <button class="btn btn-primary w-100 mt-3" type="submit" name="finish">Finish</button>
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
    <script>
        window.onload = function() {
            // Odbrojavanje od 120 sekundi
            document.getElementsByClassName('counter')[0].innerHTML = sessionStorage.getItem("i");
            const interval = setInterval(function() {
                var i = sessionStorage.getItem("i");
                i--;
                sessionStorage.setItem("i", i);
                document.getElementsByClassName('counter')[0].innerHTML = i;
                // Kada istekne vreme od 120 prekidamo setInterval
                if(sessionStorage.getItem("i")==0) {
                    clearInterval(interval);
                    document.getElementsByClassName('counter')[0].classList.add('text-danger'); // Dodavanje klase za style - crvena boja BS
                    document.getElementById('form3Example3').disabled = true; // Disable input
                    // Vracamo ga na home page
                    setInterval(function() {
                        alert("Unfortunately, your time to enter the code has expired :( You will be headed to home page.");
                        window.location = '../../index.php';
                    }, 3000);
                }
            }, 1000);
        };
    </script>
</body>

</html>