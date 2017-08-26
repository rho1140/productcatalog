<!DOCTYPE HTML>  
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Product Catalog</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link rel="stylesheet" href="css/styles.css">
<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro" rel="stylesheet">
</head>
<body>  

<?php

$username = "bc_api_username";
$password = "password";
$remote_url = 'https://store-njwk9edm.mybigcommerce.com/api/v2/products.json?include=name,sku,price,custom_url';

// Create a stream
$opts = array(
  'http'=>array(
    'method'=>"GET",
    'header' => "Authorization: Basic " . base64_encode("$username:$password")                 
  )
);

$context = stream_context_create($opts);

// Open the file using the HTTP headers set above
$file = file_get_contents($remote_url, false, $context);


echo "<h1>Product Catalog from foxguard.net</h1>";
echo "<br>";


//Find a string between 2 strings 
function getContents($str, $startDelimiter, $endDelimiter) {
  $contents = array();
  $startDelimiterLength = strlen($startDelimiter);
  $endDelimiterLength = strlen($endDelimiter);
  $startFrom = $contentStart = $contentEnd = 0;
  while (false !== ($contentStart = strpos($str, $startDelimiter, $startFrom))) {
    $contentStart += $startDelimiterLength;
    $contentEnd = strpos($str, $endDelimiter, $contentStart);
    if (false === $contentEnd) {
      break;
    }
    $contents[] = substr($str, $contentStart, $contentEnd - $contentStart);
    $startFrom = $contentEnd + $endDelimiterLength;
  }

  return $contents;
}


//Arrays of the product names, prices, skus and urls 
$productnames = ( getContents($file, "\"name\":\"", "\",\"") );

$productprices = ( getContents($file, "\"price\":\"", "\",\"") );

$productskus = ( getContents($file, "\"sku\":\"", "\",\"") );

$producturls = ( getContents($file, "\"custom_url\":\"", "\",\"") );

$imageexist = ( getContents($file, "\"primary_image\":{\"id\":", "}}") );


$end = count($productnames);
$start = 0;

function scantext($productnames, $productskus, $productprices, $producturls, $imageexist, $start, $end) {
	while($start < $end) {
		$productname = $productnames[$start];
		$productname = stripslashes($productname);
		$sku = $productskus[$start];
		$price = $productprices[$start];
		$price = substr_replace($price, "", -2);
		$url = $producturls[$start];
		$url = stripslashes($url);
		if ($imageexist[$start] == "0") {
			print "<img src=\"/images/noimage.jpg\"> ";
		} else {
			$productimages = getContents($imageexist[$start], "\"thumbnail_url\":\"", "\",\"");
			$productimages = $productimages[0];
			$productimages = stripslashes($productimages);
			print "<img src=\"$productimages\"> ";	
		}
		print "<a href=\"http://www.foxguard.net$url\">$productname</a>  /  $sku /  $price<br>";
		$start = $start  + 1;
	}
}


echo scantext($productnames, $productskus, $productprices, $producturls, $imageexist, $start, $end);

?>
</body>
</html>
