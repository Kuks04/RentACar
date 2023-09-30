<?php session_start(); 
$_SESSION['uploadPage']= 'addcar' ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rent A Car - Add Car</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../../styles/style.css">
</head>

<body class="bg-dark text-white">
    <?php
    $car = $desc = $fileName = null;

    if(isset($_SESSION['fileName']))
        $fileName = $_SESSION['fileName'];

    include '../../sql/connection.php';

    // provera da li je forma podneta
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
        // dodeljivanje vrednosti promenljivama
        $_SESSION['car'] = $car = $_POST['car'];
        $_SESSION['desc'] = $desc = $_POST['desc'];
        
        // provera da li su sva polja popunjena
        if (!empty($_POST['car']) && !empty($_POST['desc']) && $fileName != null) {
            // provera da li naslov ima manje od 5 karaktera
            if (strlen($car) < 5) {
                $errors['car'] = 'Car Brand must have more than 5 characters.';
            } else $errors['car'] = null;

            // provera da li poruka ima manje od 10 karaktera
            if (strlen($desc) < 10) {
                $errors['desc'] = 'Description must have more than 10 characters.';
            } else $errors['desc'] = null;

            // ako nema gresaka mozemo sacuvati podatke u bazi podataka
            if ($errors['car'] == null && $errors['desc'] == null && !isset($_SESSION['uploaderr'])) {
                // Insertujemo podatke u bazu
                $insert = "INSERT INTO automobil(marka, opis, slika)
                VALUES('$car', '$desc', '$fileName')";
                mysqli_query($conn, $insert);
                unset($_SESSION['fileName']);

                $select = "SELECT * FROM newsletter";
                $result = mysqli_query($conn, $select);

                // ukoliko se postavi slika, a ne dovrsimo dodavanje vozila, mora da se izbrise ta slika
                $_SESSION['success'] = true;
                // Ako ima prijavljeni na newsletter onda saljemo
                if (mysqli_num_rows($result) > 0) {
                    // svakom korisniku sa newsletter liste saljemo email adresu
                    while ($row = mysqli_fetch_assoc($result)) {
                        $to = $row['email'];
                        $subject = "We've added new car!";
                        $message = "$car<br>$desc";
                        $headers = "From:ognjenkuks@gmail.com \r\n";
                        $headers .= "MIME-Version: 1.0\r\n";
                        $headers .= "Content-type: text/html\r\n";

                        //saljemo email da smo dodali automobil
                        mail($to, $subject, $message, $headers);
                    } 
                }
                echo "<script> alert('Successfully added car.'); window.location = '../../index.php'; </script>";
            }
        } else {
            $errors['empty'] = 'Please fill in all fields and add a picture.';
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
                            <li class="breadcrumb-item active" aria-current="page">Add car</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <div class="row">
                <div class="container-xl px-4 mt-4">
                        <div class="row">
                            <div class="col-xl-4">
                                <!-- Car picture card-->
                                <div class="card bg-dark shadow-lg mb-4 mb-xl-0">
                                    <div class="card-header">Car Picture</div>
                                    <div class="card-body text-center">
                                        <!-- Profile picture image-->
                                        <?php if(isset($_SESSION['fileName'])) 
                                            echo '<img class="mb-3" style="width: 300px;" 
                                            src="../../images/cars/'. $fileName .'" alt="Profile picture">';
                                        ?>
                                        
                                        <!-- Profile picture help block-->
                                        <div class="small font-italic text-muted mb-4">JPG, SVG or PNG no larger than 5 MB</div>
                                        <!-- Profile picture upload button-->
                                        <form id="form" action="../../utils/upload.php" method="post" enctype="multipart/form-data">
                                            <button class="btn btn-primary" onclick="document.getElementById('getFile').click();" type="button">Upload image</button>
                                            <input type='file' id="getFile" name="upload" accept=".png, .jpg, .jpeg, .svg" style="display:none;"
                                                onchange="document.getElementById('form').submit();">
                                            <?php if (isset($_SESSION['uploaderr'])) echo '<p class="text-danger mt-3">' . $_SESSION['uploaderr'] . '</p>'; ?>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-8">
                                <!-- Car details card-->
                                <div class="card bg-dark shadow-lg mb-4">
                                    <div class="card-header">Car Details</div>
                                    <div class="card-body">
                                        <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                            <div class="mb-3">
                                                <label class="small mb-1" for="inputCar">Car Brand</label>
                                                <!-- U input value ubacujemo ako sesija nije postavljena vrednost $car odnosno null, jer ako sesija
                                                nije postavljena $car ce biti null, isto i u textarea -->
                                                <input name="car" id="inputCar" type="text" placeholder="Enter car brand" value="<?php if(!isset($_SESSION['car'])) echo $car; 
                                                else echo $_SESSION['car']; ?>" class="form-control <?php if (!empty($errors['car'])) echo 'is-invalid'; ?>" />
                                                <?php
                                                if (!empty($errors['car'])) {
                                                    echo '<p class="text-danger mt-3">' . $errors['car'] . '</p>';
                                                }
                                                ?>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label" for="form3Example4">Description</label><br />
                                                <textarea class="w-100 <?php if (!empty($errors['desc'])) echo 'is-invalid'; ?>" name="desc" id="form3Example4" rows="10"><?php
                                                    if(!isset($_SESSION['desc'])) echo $desc; else echo $_SESSION['desc']; ?></textarea>
                                                <?php
                                                if (!empty($errors['desc'])) {
                                                    echo '<p class="text-danger mt-3">' . $errors['desc'] . '</p>';
                                                }   
                                                ?>  
                                            </div>

                                            <button class="btn btn-primary" type="submit" name="add">Add car</button>
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
                    </form>
                </div>
            </div>
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
</body>

</html>