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

$fileInputName = "imageToUpload";
$uploaddir = '/var/www/uploads/';
$uploadfile = $uploaddir . basename($_FILES[$fileInputName]['name']);

$html = '';
if (!isset($_POST["username"]) || !isset($_POST["email"]) || !isset($_POST["cellPhone"]))
{
	$html = getDiv(getParagraph("An error occurred while processing your upload; please be sure to fill in all of the upload form fields. <a href='index.php'>Click here to return to the upload page.</a>", "inputError"));
	echo $html;
	exit;
}
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
	
	$imageDisplayContent = getParagraph("Thank you for uploading your image. You will receive an email shortly, which will give you the option to subscribe to notifications about the processing status of your image. If you subscribe, you will receive a text update when your image has been processed.", "uploadInfo");
	$imageDisplayContent .= getParagraph("Uploaded image:");
	$imageDisplayContent .= "<img src='$imageURL' alt='Your uploaded image' />";
	$imageDisplayContent .= getParagraph("Uploader Name: ".$_POST['username']);
	$imageDisplayContent .= getParagraph("Email: ".$_POST['email']);
	$imageDisplayContent .= getParagraph("Cell Phone Number: ".$_POST['cellPhone']);
	$bodyContent .= getDiv($imageDisplayContent, "uploadedImage");

	$link = getDbWriteConnection();
	if (!$link || $link == null)
	{
		die("A database error occurred: " . mysqli_error($link));
	}

	$stmt = null;

	if (!($stmt = $link->prepare("INSERT INTO uploads (id,email,phone,filename,s3rawurl,s3finishedurl,status,issubscribed) VALUES (NULL,?,?,?,?,?,?,?)")))
	{
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}
	else
	{

		$fileName = basename($_FILES[$fileInputName]['name']);
		$stmt->bind_param("sssssii",$_POST["email"],$_POST["cellPhone"],$fileName,$imageURL,"none",0,0);

		if (!$stmt->execute())
		{
			echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
		}
		else
		{
			$link->real_query("SELECT MAX(id) as maxID FROM uploads");
			$result = $link->use_result();

			$row = $result->fetch_assoc();
			$rowID = $row['maxID'];
			$bodyContent .= getDiv(getParagraph("Inserted row ID: $rowID"));
			$bodyContent .= getParagraph("Insert successful!");
		}
	}

	if ($stmt != null)
	{
		$stmt->close();
	}
}
else
{
	$bodyContent .= getDiv(getParagraph("A problem occurred while uploading the image. Please <a href='index.php'>return to the upload page</a> and try again."), "uploadStatus");
}

$html .= getBody($bodyContent);
$html .= getHtmlEnd();
echo $html;
?>