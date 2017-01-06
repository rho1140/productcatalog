<!DOCTYPE HTML>  
<html>
<head>
</head>
<body>  

<?php

$username = "lucian";
$password = "6b02cfb11c15b43b564106e3debccfcf1b41a625";
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


echo "<h2>Product Catalog from foxguard.net</h2>";


//Raw data 
/*
print($file);
*/


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

//Arrays of the product names, prices, skus and urls merged into one array 
$productnames = ( getContents($file, "\"name\":\"", "\",\"") );

$productprices = ( getContents($file, "\"price\":\"", "\",\"") );

$productskus = ( getContents($file, "\"sku\":\"", "\",\"") );

$producturls = ( getContents($file, "\"custom_url\":\"", "\",\"") );

$productdata = array_merge($productnames, $productprices, $productskus, $producturls);

$ini = count($productdata);
$end = $ini /= 4;
$start = 0;

function scanarray($productnames, $productskus, $productprices, $producturls, $start, $end) {
	while($start < $end) {
		$productname = $productnames[$start];
		$sku = $productskus[$start];
		$price = $productprices[$start];
		$url = $producturls[$start];
		$url = stripslashes($url);
		print "Name: <a href=\"http://www.foxguard.net$url\">$productname</a> SKU: $sku Price: $price $url <br>";
		$start  = $start  + 1;
	}
}


echo scanarray($productnames, $productskus, $productprices, $producturls, $start, $end);


?>
</body>
</html>



