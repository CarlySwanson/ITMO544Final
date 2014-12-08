<?php
include ("common.php");
session_start();
// Name: Carly Swanson
// Class: ITMO 544
// Assignment: Final Assignment
$html = '';
$html .= getDoctype();
$html .= getHtmlStart();
$html .= getHeader("Upload an Image");
$html .= getBody(getDiv(getUploadForm(), "formContainer") . getDiv(getGalleryForm()));
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
	$html .= '<input type="email" name="email" id="email" />';
	$html .= getLabel("cellPhone", "Cell Phone Number:");
	$html .= '<input type="tel" name="cellPhone" id="cellPhone" />';
	$html .= '<input type="hidden" name="MAX_FILE_SIZE" value="30000" />';
	$html .= getLabel($fileInputName, "Please choose a file to upload:");
	$html .= "<input type='file' name='$fileInputName' />";
	$html .= '<input type="submit" value="Upload Image" />';
	$html .= "</form>";
	return $html;
}

function getGalleryForm()
{
	$html = '';
	$html .= '<form id="galleryForm" action="gallery.php" method="post">';
	$html .= getParagraph("Enter your email address to see all of your previous uploads.");
	$html .= getLabel("galleryEmail", "Email:");
	$html .= '<input type="submit" value="View Gallery" />';
	$html .= '</form>';
	return $html;
}

function getLabel($labelFor, $labelText)
{
	$html = '';
	$html .= "<label for='$labelFor'>$labelText</label>";
	return $html;
}
?>
