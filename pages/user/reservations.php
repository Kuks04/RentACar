<?php
session_start();
// Ako korisnik nije ulogvan ne moze da rezervise
if (!isset($_SESSION['fname'])) {
    $_SESSION['carid'] = $_GET['id'];
    echo "<script> window.location = '../authentication/login.php'; </script>";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rent A Car - Reservations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
    <link rel="stylesheet" href="../../styles/style.css">
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var calendarEl = document.getElementById('calendar');
            var startDate, endDate;
            
            // funkcija za formatiranje datuma u obliku yyyy-mm-dd
            function formatDate(date) {
                var d = new Date(date),
                    month = '' + (d.getMonth() + 1),
                    day = '' + d.getDate(),
                    year = d.getFullYear();

                if (month.length < 2)
                    month = '0' + month;
                if (day.length < 2)
                    day = '0' + day;

                return [year, month, day].join('-');
            }

            function DateSelected() {
                document.getElementById('startDate').value = formatDate(startDate);
                document.getElementById('endDate').value = formatDate(endDate);
            }

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                validRange: {
                    start: new Date(), // Ne mozemo rezervisati za proslo vreme
                    end: '2026-01-31' // Datum do kog se moze birati rezervacija
                },
                events: [
                    <?php
                    // Biramo zauzete datume iz tabele datumi
                    include '../../sql/connection.php';
                    $select = "SELECT datum FROM datumi 
                        WHERE id_rezervacije IN (SELECT id_rezervacije FROM rezervacija
                        WHERE id_automobila = " . $_GET['id'] . ")";
                    $result = mysqli_query($conn, $select);
                    
                    $i = 1;
                    while ($row = mysqli_fetch_assoc($result)) {
                        // Ovaj if sluzi za stavljanje zareza posle svakog datuma
                        if ($i == 0) { // Prvi prolaz kroz while se ne postavlja zarez
                            echo ',';
                        }
                        $i = 0;
                        echo '{
                                title: "Reserved",
                                date: "' . $row['datum'] . '"
                            }';
                    }
                    ?>
                ],
                selectable: true,
                unselectCancel: "#calendar",
                selectOverlap: function (event) { // Ne mogu se selektovati rezervisani datumi
                    return event.rendering === 'background';
                },
                select: function (info) {
                    startDate = info.start;
                    endDate = info.end;
                    DateSelected();
                }
            });
            calendar.render();
        });

        function Book() {
            if (confirm('Are you sure you want to make this Reservation?')) {
                document.getElementById('confirmBtn').click();
            }
        }
    </script>
</head>

<body class="bg-dark text-white">
    <?php 
    // Proverava da li je forma podneta
    if (isset($_POST['confirm'])) {
        include '../../sql/connection.php';
        $startDate = new DateTime($_POST['startDate']);
        $endDate = new DateTime($_POST['endDate']);
        $id_korisnika = $_SESSION['id'];
        $id_automobila = $_GET['id'];

        // upisujemo rezervaciju
        $insert = "INSERT INTO rezervacija(id_korisnika, id_automobila)
        VALUES('$id_korisnika', '$id_automobila')";
        mysqli_query($conn, $insert);

        // sada moramo da napravimo select upit da bi uzeli id rezervacije da bismo
        // ubacili datume, pre toga ne znamo koji je id rezervacije jer je auto increment
        $select = "SELECT id_rezervacije FROM rezervacija ORDER BY id_rezervacije DESC LIMIT 1";
        $value = mysqli_fetch_assoc(mysqli_query($conn, $select));

        // Sada dodajemo datume koji su izabrani za tu rezervaciju
        while ($startDate < $endDate) {
            $insert = "INSERT INTO datumi(datum, id_rezervacije)
            VALUES('" . date_format($startDate,'Y-m-d') . "', '" . $value['id_rezervacije'] . "')";
            mysqli_query($conn, $insert);
            date_modify($startDate, '+1 day');
        }
        echo "<script> alert('You\'ve booked your car successfully.'); window.location='./myreservations.php'; </script>";   
    }
    ?>
    <section class="reservations">
        <div class="container py-5">
            <div class="row">
                <div class="col">
                    <nav aria-label="breadcrumb" class="bg-dark shadow-lg rounded-3 p-3 mb-4">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="../../index.php">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Reservations</li>
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
                                <div class="card-header">Car Details</div>
                                <div class="card-body text-center">
                                    <?php
                                    // Prikazujemo odabrani auto
                                    $select = "SELECT * FROM automobil WHERE id = '" . $_GET['id'] . "'";
                                    $value = mysqli_fetch_assoc(mysqli_query($conn, $select));
                                    echo '<img class="img-fluid object-fit-cover" 
                                        src="../../images/cars/' . $value['slika'] . '" alt="' . $value['opis'] . '">
                                        <h5 class="mt-3 card-title">' . $value['marka'] . '</h5>';
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-8">
                            <!-- Reservations details card-->
                            <div class="card bg-dark shadow-lg mb-4">
                                <div class="card-header">Reservation Details</div>
                                <div class="card-body">
                                    <div id='calendar'></div>
                                    <p class="mt-4">Click and drag to select multiple days.</p>
                                    <form method="POST" action="">
                                        <!-- Ove inpute stavljamo na display none jer selektujemo datume preko kalendara,
                                            ali inputi ce nam trebati da prenesemo podatke u php -->
                                        <input type="date" class="form-control d-none" name="startDate" id="startDate">
                                        <input type="date" class="form-control d-none" name="endDate" id="endDate">

                                        <!-- Dodajemo jos jedno hidden dugme da bismo mogli prvo da izbacimo 
                                            confirm alert da li je korisnik siguran -->
                                        <button class="btn btn-primary" type="button" onclick="Book();">Confirm
                                            Reservation</button>
                                        <button class="d-none" type="submit" name="confirm" id="confirmBtn"></button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
        integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
        integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF"
        crossorigin="anonymous"></script>
</body>

</html>