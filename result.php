<?php
session_start();
// Include the SDK using the Composer autoloader
require 'vendor/autoload.php';
// Use the S3Client class from the AWS SDK
use Aws\S3\S3Client;
// Name: Carly Swanson
// Class: ITMO 544
// Assignment: Final Assignment
include ("common.php");
if (isset($_POST["username"]))
{
	$_SESSION["username"] = $_POST["username"];
}

if(isset($_POST["email"]))
{
	$_SESSION["email"] = $_POST["email"];
}

if(isset($_POST["cellPhone"]))
{
	$_SESSION["cellPhone"] = $_POST["cellPhone"];
}
$fileInputName = "imageToUpload";
$uploaddir = '/var/www/uploads/';
$uploadfile = $uploaddir . basename($_FILES[$fileInputName]['name']);

$html = '';
$html .= getDoctype();
$html .= getHtmlStart();
$html .= getHeader("Upload Result");
$bodyContent = '';
if (move_uploaded_file($_FILES[$fileInputName]['tmp_name'], $uploadfile)) {
	$client = S3Client::factory();
	$imageURL = '';
	$bodyContent .= getDiv(getParagraph("File is valid, and was successfully uploaded."), "uploadStatus");
	$bodyContent .= getDiv(getParagraph(print_r($_FILES, true)), "fileDetails");
	$bucket = uniqid("Swanson-Carly-MA4-", true);
	$bucketProcessingContent = getParagraph("Creating bucket named $bucket.", "bucketCreation");
	$result = $client->createBucket(array(
    		'Bucket' => $bucket
	));
	// Wait for the bucket to be created before setting its ACL
	$client->waitUntilBucketExists(array('Bucket' => $bucket));
	$client->putBucketAcl(array(
		'Bucket' => $bucket,
		'ACL' => 'public-read'
	));
	$key = basename($_FILES[$fileInputName]['name']);
	$bucketProcessingContent .= getParagraph("Creating a new object with key $key", "objectCreation");
	$result = $client->putObject(array(
    		'Bucket' => $bucket,
    		'Key'    => $key,
    		'SourceFile' => $uploadfile
	));
	$imageURL = $result['ObjectURL'];
	$bodyContent .= getDiv($bucketProcessingContent, "bucketProcessing");
	$client->waitUntil('ObjectExists', array(
		'Bucket' => $bucket,
		'Key' => $key
	));
	$client->putObjectAcl(array(
		'Bucket' => $bucket,
		'Key' => $key,
		'ACL' => 'public-read'
	));
	$imageDisplayContent = getParagraph("Uploaded image:");
	$imageDisplayContent .= "<img src='$imageURL' alt='Your uploaded image' />";
	$imageDisplayContent .= getParagraph("Uploader Name: ".$_SESSION['username']);
	$imageDisplayContent .= getParagraph("Email: ".$_SESSION['email']);
	$imageDisplayContent .= getParagraph("Cell Phone Number: ".$_SESSION['cellPhone']);
	$bodyContent .= getDiv($imageDisplayContent, "uploadedImage");
} else {
	$bodyContent .= getDiv(getParagraph("A problem occurred while uploading the image. Please <a href='index.php'>return to the upload page</a> and try again."), "uploadStatus");
}

$html .= getBody($bodyContent);
$html .= getHtmlEnd();
echo $html;
?>
