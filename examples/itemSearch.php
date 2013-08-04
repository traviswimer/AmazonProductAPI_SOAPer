<?php
require('../AmazonApi.php');

//Create API access object
$public_key = 'YOUR PUBLIC KEY';
$secret_key = 'YOUR PRIVATE KEY';
$associate_tag = 'AN OPTIONAL ASSOCIATE TAG';
$amazon_api = new AmazonAPI($public_key, $secret_key, $associate_tag);

//Array of request parameters
$params_array = array(
	'Operation' => 'ItemSearch',
	'SearchIndex' => 'Music',
	'Keywords' => urlencode('Slow Magic')
);

// returns a list of items for the search query 'Slow Magic'
$response = $amazon_api->sendRequest($params_array);

echo '<pre>';
print_r($response);
echo '</pre>';