<?php session_start();
// Brisemo sesiju carid jer imamo if(isset(ses carid)) u login page,
// koja nam sluzi samo ako je korisnik pokusao da rezervise auto a da nije ulogovan
// pa je preusmeren na login page
unset($_SESSION['carid']);

// sesija car i desc sluze kao placeholderi na stranici add car, jer 
// imamo upload slike pa se resfreshuju inputi
unset($_SESSION['car']);
unset($_SESSION['desc']);

// Ako nije izvresno dodavanje vozila brisemo uploadovanu sliku vozila
if (!isset($_SESSION['success']) && isset($_SESSION['fileName']))
    unlink('./images/cars/' . basename($_SESSION['fileName']));
unset($_SESSION['success']);
unset($_SESSION['fileName']); ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Rent A Car</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="./styles/style.css">
</head>

<body class="bg-dark text-white">
    <?php
    // proveramo da li se sad ulogovao, onda bi se sacuvalo u sesiji
    // u grananju ispod cilj nam je da prikazemo buttone ako je korisnik ulogovan ili nije, i da prikazemo napredne opcije adminu
    if (!isset($_SESSION['fname'])) {
        // proveravamo da li se prijavio u prethodnih 2 sata ako je cekirao ~Remember me
        if (!isset($_COOKIE['fname'])) {
            echo "<style>
                .admin, .loggedin { display: none; }
                .notloggedin { display: ''; }
            </style>";
        } else {
            $_SESSION['id'] = $_COOKIE['id'];
            $_SESSION['fname'] = $_COOKIE['fname'];
            $_SESSION['lname'] = $_COOKIE['lname'];
            $_SESSION['email'] = $_COOKIE['email'];
            $_SESSION['pass'] = $_COOKIE['pass'];
            $_SESSION['img'] = $_COOKIE['img'];

            // proveravamo da li je admin da bi dobio napredne funkcije
            if (isset($_COOKIE['admin'])) {
                $_SESSION['admin'] = $_COOKIE['admin'];
            }

            echo "<style>
                    .loggedin { display: ''; }
                    .notloggedin { display: none; }
                </style>";

            // proveravamo da li je admin da bi dobio napredne funkcije
            if (isset($_SESSION['admin'])) {
                echo "<style>
                    .admin { display: ''; }
                </style>";
            } else {
                echo "<style>
                        .admin { display: none; }
                    </style>";
            }
        }
    } else {
        echo "<style>
                    .loggedin { display: ''; }
                    .notloggedin { display: none; }
                </style>";

        // proveravamo da li je admin da bi dobio napredne funkcije
        if (isset($_SESSION['admin'])) {
            echo "<style>
                    .admin { display: ''; }
                </style>";
        } else {
            echo "<style>
                    .admin { display: none; }
                </style>";
        }
    }
    echo "<style>.dropdown-item:hover {
        background-color: #6c757d;
    }</style>";
    ?>
    <!-- Navigacioni bar -->
    <nav class="navbar navbar-expand-lg w-75 mx-auto navbar-dark">
        <a class="navbar-brand" href="./index.php">RentA<span class="text-primary">Car</span></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse mt-3 mt-md-0" id="navbarSupportedContent">
            <form class="d-flex my-auto ms-2">
                <div class="input-group shadow-lg">
                    <input type="search" class="form-control bg-dark text-white border-primary" placeholder="Search"
                        aria-label="Search" id="searchbar" onkeyup="Search();" />
                    <span class="input-group-text bg-primary border-0"><i class="fa fa-search"></i></span>
                </div>
            </form>
            <ul class="navbar-nav ms-auto mb-lg-0 d-flex">
                <li class="nav-item d-flex">
                    <a class="nav-link my-auto active" aria-current="page" href="./index.php">Home</a>
                </li>
                <li class="nav-item d-flex mb-2 mb-md-0">
                    <a class="btn btn-outline-primary py-1 my-auto admin ms-2" href="./pages/admin/addcar.php">+ Add
                        car</a>
                </li>
                <li class="nav-item d-flex mb-2 mb-md-0">
                    <a class="btn btn-outline-primary py-1 my-auto admin ms-2" href="./pages/admin/newsletter.php">Send
                        mail</a>
                </li>
                <li class="nav-item d-flex">
                    <a class="btn btn-primary notloggedin ms-2" href="./pages/authentication/login.php">Login</a>
                </li>
                <li class="nav-item d-flex">
                    <a class="btn btn-outline-primary notloggedin ms-2"
                        href="./pages/authentication/register.php">Register</a>
                </li>

                <li class="nav-item dropdown loggedin">
                    <a class="nav-link dropdown-toggle ms-2" href="#" id="navbarDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="./images/icons/<?php echo $_SESSION['img'] ?>" class="rounded-circle me-1"
                            alt="Avatar" style="width: 34px; height: 34px;">
                        <?php if (isset($_SESSION['fname']))
                            echo $_SESSION['fname'] . " " . $_SESSION['lname']; ?>
                    </a>
                    <ul class="dropdown-menu bg-dark" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item text-white" href="./pages/user/profile.php">Profile</a></li>
                        <li><a class="dropdown-item text-white" href="./pages/user/myreservations.php">Your
                                reservations</a></li>
                        <li>
                            <hr class="dropdown-divider bg-white">
                        </li>
                        <li><a class="dropdown-item text-white" href="./pages/authentication/logout.php">Log Out</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
    <section id="cars">
        <h2 class="text-center my-5">Cars</h2>
        <?php
        include './sql/connection.php';
        $select = "SELECT * FROM automobil";
        $result = mysqli_query($conn, $select);
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
            $data = array_reverse($data, true);
            // Dodajemo automobile iz baze na home page
            foreach ($data as $row) {
                echo '<div class="card shadow-lg my-5 w-75 mx-auto border-0">
                <div class="row g-0">
                    <div class="col-md-4">
                        <img src="./images/cars/' . $row['slika'] . '" class="img-fluid object-fit-cover" alt="Car">
                    </div>
                    <div class="col-md-8">
                        <div class="card-body bg-dark text-white h-100">
                            <h5 class="card-title">' . $row['marka'] . '</h5>
                            <p class="card-text mb-5">' . $row['opis'] . '</p>
                            <form action="./pages/user/reservations.php?id=' . $row['id'] . '" method="post">
                                <button type="submit" class="btn btn-primary text-white position-absolute px-4 py-1 mb-3 ms-1 bottom-0" name="book">Book a car</button>
                            </form>
                            <form id="form' . $row['id'] . '" action="./pages/admin/delete.php?id=' . $row['id'] . '" method="post">
                                <button type="button" class="btn btn-primary-outline text-danger position-absolute p-0 mb-3 me-3 bottom-0 end-0 admin" 
                                    onclick="Delete(' . $row['id'] . ',\'car\');">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
                </div>';
            }
        } else {
            echo '<h5 class="card-title text-center">No cars yet.</h5>';
        }
        ?>
    </section>
    <?php mysqli_close($conn); ?>
    <script src="./scripts/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
        integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
        integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF"
        crossorigin="anonymous"></script>
</body>

</html>