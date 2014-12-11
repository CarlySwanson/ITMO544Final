<?php
// Include the SDK using the Composer autoloader
require 'vendor/autoload.php';
// Use the SqsClient class from the AWS SDK
use Aws\Sqs\SqsClient;
// Name: Carly Swanson
// Class: ITMO 544
// Assignment: Final Assignment
include ("common.php");

$client = SqsClient::factory(array(
	'region' => 'us-east-1'
));

$queueURL = getQueueURL();

while (true)
{
	$result = $client->receiveMessage(array(
		'QueueUrl' => "$queueURL",
		'MaxNumberOfMessages' => 1,
		'VisibilityTimeout' => 30));

	foreach ($result->getPath('Messages/*/Body') as $messageBody)
	{
		$link = getDbReadConnection(1);
		$rowID = $messageBody;
		if (!($stmt = $link->prepare("SELECT * FROM uploads WHERE id = '?'")))
		{
			echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
		else
		{
			$stmt->bind_param($rowID);
		}
		echo $messageBody ."\n";
	}
}
?>
