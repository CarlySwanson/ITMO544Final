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
?>
