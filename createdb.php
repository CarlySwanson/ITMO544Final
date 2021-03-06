<?php
include ("common.php");
// Name: Carly Swanson
// Class: ITMO 544
// Assignment: Final Assignment
$link = getDbWriteConnection() or die("Error " . mysqli_error($link));

if (mysqli_connect_errno())
{
	printf("Connect failed: %s\n", mysqli_connect_error());
	exit();
}

$create_table = 'CREATE TABLE IF NOT EXISTS uploads
(
	id INT NOT NULL AUTO_INCREMENT,
	email VARCHAR(200) NOT NULL,
	phone VARCHAR(20) NOT NULL,
	filename VARCHAR(255) NOT NULL,
	s3rawurl VARCHAR(255) NOT NULL,
	s3finishedurl VARCHAR(255) NOT NULL,
	status INT NOT NULL,
	issubscribed INT NOT NULL,
	PRIMARY KEY(id)
)';

$create_tbl = $link->query($create_table);

if ($create_table)
{
	echo "Table is created or No error returned.";
}
else
{
	echo "An error occurred while creating the database table.";
}
$link->close();
?>