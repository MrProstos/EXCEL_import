<?php
$conn = mysqli_connect("127.0.0.1:9306", "", "", "");
if (mysqli_connect_errno())
    die("failed to connect to Sphinx: " . mysqli_connect_error());

$res = mysqli_query($conn, "SHOW VARIABLES");
while ($row = mysqli_fetch_row($res))
    print "$row[0]: $row[1]\n";