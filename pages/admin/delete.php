<?php  
include '../../sql/connection.php';
$id=$_GET['id'];
// vadimo ime slike da bismo je obrisali
$select = "SELECT slika FROM automobil WHERE id='$id'";
$value = mysqli_fetch_assoc(mysqli_query($conn, $select));


// Prvo moramo da izbrisemo rezervacije pa tek onda automobil
$select = "SELECT id_rezervacije FROM rezervacija WHERE id_automobila='$id'";
$result = mysqli_query($conn, $select);
if(mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        $delete = "DELETE FROM datumi WHERE id_rezervacije=".$row['id_rezervacije'];
        mysqli_query($conn, $delete);
    }
    $deleteReservation = "DELETE FROM rezervacija WHERE id_automobila='$id'";
    mysqli_query($conn, $deleteReservation);
}

$deleteCar = "DELETE FROM automobil WHERE id='$id'";
// Brisemo automobil i takodje i sliku tog automobila
if (mysqli_query($conn, $deleteCar)) {
    unlink('../../images/cars/' . basename($value['slika']));
    echo "<script>alert('You have successfully deleted the car.'); window.location = '../../index.php'; </script>";
}
?>