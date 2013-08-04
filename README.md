This is an extremely simple class used to make requests to the Amazon Product API. There don't seem to be many good resources available to get people started using the API, so hopefully this code will be of help.

A list of Amazon Product Advertising API operations can be found here:
http://docs.aws.amazon.com/AWSECommerceService/latest/DG/CHAP_OperationListAlphabetical.html

# Example usage

The code below shows how to search Amazon's music section for the band "Slow Magic".

```php
//Create API access object
$public_key = 'YOUR PUBLIC KEY';
$secret_key = 'YOUR PRIVATE KEY';
$associate_tag = 'AN OPTIONAL ASSOCIATE TAG';
$amazon_api = new AmazonAPI($public_key, $secret_key, $associate_tag);

// Set the API request parameters
$params_array = array(
	'Operation' => 'ItemSearch',
	'SearchIndex' => 'Music',
	'Keywords' => urlencode('Slow Magic')
);

// returns a list of items for the search query 'Slow Magic'
$response = $amazon_api->sendRequest($params_array);
```