<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Rent A Car - My Reservations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../../styles/style.css">
</head>

<body class="bg-dark text-white">
    <section class="reservations">
        <div class="container py-5">
            <div class="row">
                <div class="col">
                    <nav aria-label="breadcrumb" class="bg-dark shadow-lg rounded-3 p-3 mb-4">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="../../index.php">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">My Reservations</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <h5 class="card-title">My Reservations - <?php echo $_SESSION['fname'] . " " . $_SESSION['lname']; ?></h5>
            <?php
            include '../../sql/connection.php';
            $select = "SELECT id_rezervacije, id_automobila FROM rezervacija
            WHERE id_korisnika =" . $_SESSION['id'];
            $result = mysqli_query($conn, $select);

            // Proveravamo da li ima neku rezervaciju
            if (mysqli_num_rows($result) > 0) {
                // Ako ima onda ispisujemo
                while ($row = mysqli_fetch_assoc($result)) {
                    // Ovde selektujemo sliku i marku vozila koje je rezervisano
                    $select = "SELECT * FROM automobil
                    WHERE id = " . $row['id_automobila'];
                    $car = mysqli_fetch_assoc(mysqli_query($conn, $select));

                    echo '<div class="card shadow-lg my-5 w-75 mx-auto border-0">
                    <div class="row g-0">
                        <div class="col-md-4">
                            <img src="../../images/cars/' . $car['slika'] . '" class="img-fluid object-fit-cover" alt="Car">
                        </div>
                        <div class="col-md-8">
                            <div class="card-body bg-dark text-white h-100">
                                <h5 class="card-title">' . $car['marka'] . '</h5>
                                <p class="card-text">Date of reservation:</p>
                                <p class="card-text">';

                    // Ovde selektujemo datume koje je rezervisao za tu rezervaciju
                    $select = "SELECT * FROM datumi
                    WHERE id_rezervacije =" . $row['id_rezervacije'];
                    $query = mysqli_query($conn, $select);
                    
                    // Datumi kada je rezervisao
                    while ($row1 = mysqli_fetch_assoc($query)) {
                        echo $row1['datum'] . '<br>';
                    }
                    echo '</p><form id="form'.$row['id_rezervacije'].'" action="./deletereservation.php?id=' . $row['id_rezervacije'] . '" method="post">
                                    <button type="button" class="btn btn-primary-outline text-danger position-absolute p-0 mb-3 me-3 bottom-0 end-0" 
                                        onclick="Delete('.$row['id_rezervacije'].',\'reservation\');">Delete reservation</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    </div>';
                }
            } else {
                echo '<h5 class="card-title">You do not have any reservations.</h5>';
            }
            ?>
        </div>
    </section>
    <?php mysqli_close($conn); ?>
    <script src="../../scripts/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
        integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
        integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF"
        crossorigin="anonymous"></script>
</body>

</html>