<?php
// Name: Carly Swanson
// Class: ITMO 544
// Assignment: Final Assignment
function getDoctype()
{
	$html = "<!DOCTYPE html>";
	return $html;
}

function getHtmlStart()
{
	$html = "<html>";
	return $html;
}

function getHeader($pageTitle = '')
{
	$html = '';
	$html .= "<head>";
	$html .= "<title>ITMO 544 - Carly Swanson - $pageTitle</title>";
	$html .= "</head>";
	return $html;
}

function getBody($bodyContent)
{
	$html = '';
	$html .= "<body>";
	$html .= $bodyContent;
	$html .= "</body>";
	return $html;
}

function getDiv($divContent, $divClass = 'container')
{
	$html = '';
	$html .= "<div class='$divClass'>";
	$html .= $divContent;
	$html .= "</div>";
	return $html;
}

function getParagraph($pContent, $pClass = 'defaultParagraph')
{
	$html = '';
	$html .= "<p class='$pClass'>";
	$html .= $pContent;
	$html .= "</p>";
	return $html;
}

function getHtmlEnd()
{
	$html = "</html>";
	return $html;
}

function getQueueURL()
{
	$queueURL = 'MODLEVEL2queueURLMODLEVEL2';
	return $queueURL;
}

function getDatabaseEndpoint()
{
	$dbEndpoint = 'MODLEVEL2dbURLMODLEVEL2';
	return $dbEndpoint;
}

function getDbWriteConnection()
{
	$link = mysqli_connect(getDatabaseEndpoint(), "thedoctor", "ilovethetardis", "ImageProcessing", 3306);
	return $link;
}

function getDbReadConnection($replicaNumber = 1)
{
	$endpoint = getDatabaseEndpoint();
	$firstDotIndex = strpos($endpoint, '.');
	$modifiedEndpoint = substr($endpoint, 0, $firstDotIndex) . $replicaNumber . substr($endpoint, $firstDotIndex);
	$link = mysqli_connect($modifiedEndpoint, "thedoctor", "ilovethetardis", "ImageProcessing", 3306);
	return $link;
}
?>
