<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rent A Car - Newsletter</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../../styles/style.css">
</head>

<body class="bg-dark text-white">
    <?php
    $subject = $message = null;

    // provera da li je forma podneta
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // dodeljivanje vrednosti promenljivama
        $subject = $_POST['subject'];
        $message = $_POST['message'];

        // provera da li su sva polja popunjena
        if (!empty($_POST['subject']) && !empty($_POST['message'])) {
            // provera da li naslov ima manje od 5 karaktera
            if (strlen($subject) < 5) {
                $errors['subject'] = 'Subject must have more than 5 characters.';
            } else $errors['subject'] = null;

            // provera da li poruka ima manje od 10 karaktera
            if (strlen($message) < 10) {
                $errors['message'] = 'Message must have more than 10 characters.';
            } else $errors['message'] = null;

            // ako nema gresaka mozemo sacuvati podatke u bazi podataka
            if ($errors['subject'] == null && $errors['message'] == null) {
                include '../../sql/connection.php';
                $select = "SELECT * FROM newsletter";
                $result = mysqli_query($conn, $select);
                if ($result->num_rows > 0) {
                    // svakom korisniku sa newsletter liste saljemo email adresu
                    while ($row = $result->fetch_assoc()) {
                        $to = $row['email'];
                        $message .= '<br><p>Rent A <span style="color:blue;">Car</span> Company!</p>';
                        $headers = "From:ognjenkuks@gmail.com \r\n";
                        $headers .= "MIME-Version: 1.0\r\n";
                        $headers .= "Content-type: text/html\r\n";

                        //saljemo email za neko obavestenje
                        mail($to, $subject, $message, $headers);
                    } 
                    echo "<script>alert('All mails have been sent!'); window.location = '../../index.php'; </script>";
                }
            }
        } else {
            $errors['empty'] = 'Please fill in all fields.';
        }
    }

    ?>

    <section class="newsletter">
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
                                        Send Email
                                    </h1>

                                    <!-- Subject input -->
                                    <div class="form-outline mb-4">
                                        <label class="form-label" for="form3Example3">Subject</label>
                                        <input type="text" id="form3Example3" name="subject" value="<?php echo $subject; ?>" 
                                            class="form-control <?php if (!empty($errors['subject'])) { echo 'is-invalid'; }?>" />
                                        <?php
                                        if (!empty($errors['subject'])) {
                                            echo '<p class="text-danger mt-3">' . $errors['subject'] . '</p>';
                                        }
                                        ?>
                                    </div>

                                    <!-- Message input -->
                                    <div class="form-outline mb-4">
                                        <label class="form-label" for="form3Example4">Message</label><br />
                                        <textarea class="w-100 <?php if (!empty($errors['message'])) { echo 'is-invalid'; }?>"
                                            name="message" id="form3Example4" rows="10" ><?php echo $message; ?></textarea>
                                        <?php
                                        if (!empty($errors['message'])) {
                                            echo '<p class="text-danger mt-3">' . $errors['message'] . '</p>';
                                        }
                                        ?>
                                    </div>

                                    <!-- Send mail button -->
                                    <button type="submit" class="btn btn-primary btn-block px-3 py-2 mb-4" name="send">
                                        Send mail
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