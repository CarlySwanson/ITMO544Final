<?php
include ("common.php");
session_start();
// Name: Carly Swanson
// Class: ITMO 544
// Assignment: Mini-Assignment 4
$html = '';
$html .= getDoctype();
$html .= getHtmlStart();
$html .= getHeader("Upload an Image");
$html .= getBody(getDiv(getUploadForm(), "formContainer"));
$html .= getHtmlEnd();
echo $html;

function getUploadForm()
{
	$fileInputName = "imageToUpload";
	$html = '';
	$html .= '<form id="uploadForm" action="result.php" method="post" enctype="multipart/form-data">';
	$html .= getLabel("username", "Your Name:");
	$html .= '<input type="text" name="username" id="username" />';
	$html .= getLabel("email", "Email:");
	$html .= '<input type="text" name="email" id="email" />';
	$html .= getLabel("cellPhone", "Cell Phone Number:");
	$html .= '<input type="text" name="cellPhone" id="cellPhone" />';
	$html .= '<input type="hidden" name="MAX_FILE_SIZE" value="30000" />';
	$html .= getLabel($fileInputName, "Please choose a file to upload:");
	$html .= "<input type='file' name='$fileInputName' />";
	$html .= '<input type="submit" value="Upload Image" />';
	$html .= "</form>";
	return $html;
}

function getLabel($labelFor, $labelText)
{
	$html = '';
	$html .= "<label for='$labelFor'>$labelText</label>";
	return $html;
}
?>
