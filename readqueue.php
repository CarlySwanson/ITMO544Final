<?php
// Include the SDK using the Composer autoloader
require 'vendor/autoload.php';
// Use the SqsClient class from the AWS SDK
use Aws\Sqs\SqsClient;
// Use the S3Client class from the AWS SDK
use Aws\S3\S3Client;
// Name: Carly Swanson
// Class: ITMO 544
// Assignment: Final Assignment
include ("common.php");

$client = SqsClient::factory(array(
	'region' => 'us-east-1'
));

$s3rawurl = '';
$queueURL = getQueueURL();

while (true)
{
	$result = $client->receiveMessage(array(
		'QueueUrl' => "$queueURL",
		'MaxNumberOfMessages' => 1,
		'VisibilityTimeout' => 30));

	$rowID = '';

	foreach ($result->getPath('Messages/*/Body') as $messageBody)
	{
		$link = getDbReadConnection(1) or die("A read database error occurred: " . mysqli_error($link));
		$rowID = $messageBody;
		$stmt = null;
		if (!($stmt = $link->prepare("SELECT s3rawurl FROM uploads WHERE id = '?'")))
		{
			echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
		else
		{
			$stmt->bind_param($rowID);
			if (!$stmt->execute())
			{
				echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
			}
			else
			{
				$id = '';
				$email = '';
				$phone = '';
				$filename = '';
				$s3finishedurl = '';
				$status = '';
				$issubscribed = '';
				if (!($row = $stmt->bind_result($id, $email, $phone, $filename, $s3rawurl, $s3finishedurl, $status, $issubscribed)))
				{
					echo "Could not bind SQL result.";
				}
				else
				{
					$stmt->fetch();
					header('Content-Type: image/png');
					$img = LoadPng($s3rawurl);
					imagepng($img,"/tmp/g5.png");

					$stmt->close();
					$link->close();

					$s3client = S3Client::factory();
					$imageURL = '';
					$bucket = uniqid("Swanson-Carly-final-mod-", true);
					$result = $s3client->createBucket(array(
    						'Bucket' => $bucket
					));

					// Wait for the bucket to be created before setting its ACL
					$s3client->waitUntilBucketExists(array('Bucket' => $bucket));
					$s3client->putBucketAcl(array(
						'Bucket' => $bucket,
						'ACL' => 'public-read'
					));

					$key = basename($s3rawurl);
					$result = $s3client->putObject(array(
    						'Bucket' => $bucket,
    						'Key'    => $key,
    						'SourceFile' => "/tmp/g5.png"
					));

					$s3client->waitUntil('ObjectExists', array(
						'Bucket' => $bucket,
						'Key' => $key
					));
					$s3client->putObjectAcl(array(
						'Bucket' => $bucket,
						'Key' => $key,
						'ACL' => 'public-read'
					));
					$s3finishedurl = $result['ObjectURL'];

					$writeLink = getDbWriteConnection() or die("A write database error occurred: " . mysqli_error($link));
					$updateStmt = null;
					if (!($updateStmt = $writeLink->prepare("UPDATE uploads SET s3finishedurl = '?' WHERE id = '?'")))
					{
						echo "Statement preparation failed.";
					}
					else
					{
						$updateStmt->bind_param($s3finishedurl, $rowID);
						if (!($updateStmt->execute()))
						{
							echo "Execute failed: (" . $updateStmt->errno . ") " . $updateStmt->error;
						}
						else
						{
							$receiptHandle = "";
							foreach ($result->getPath('Messages/*/ReceiptHandle') as $receiptHandle)
							{
								echo $receiptHandle ."\n";
							}
							$result = $client->deleteMessage(array(
    								'QueueUrl' => "$queueURL",
   							 	'ReceiptHandle' => "$receiptHandle"
							));
							// Publish SNS update
						}
					}
				}
			}
		}
	}
}

function LoadPng($imgname)
{
	/* Attempt to open */
	$im = @imagecreatefrompng($imgname);
	/* See if it failed */
	if(!$im)
	{
		/* Create a black image */
		$im = imagecreatetruecolor(150, 30);
		$bgc = imagecolorallocate($im, 255, 255, 255);
		$tc = imagecolorallocate($im, 0, 0, 0);
		imagefilledrectangle($im, 0, 0, 150, 30, $bgc);
		/* Output an error message */
		imagestring($im, 1, 5, 5, 'Error loading ' . $imgname, $tc);
	}
	return $im;
}
?>
