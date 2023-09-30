<?php  
include '../../sql/connection.php';
$id=$_GET['id'];
$delete = "DELETE FROM datumi WHERE id_rezervacije='$id';
    DELETE FROM rezervacija WHERE id_rezervacije='$id'";
    
// Koristimo multi query zato sto brisemo i datume i rezervaciju
if (mysqli_multi_query($conn, $delete)) {
    echo "<script>alert('You have successfully deleted your reservation.'); window.location = './myreservations.php'; </script>";
}
?>