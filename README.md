# aaPanel API PHP Client

A PHP client library for interacting with the aaPanel API.

## Installation

Install the library using Composer:

```bash
composer require azozzalfiras/aapanel-api
```

### Usage

Initialize the Client

```php
require_once 'vendor/autoload.php';

use aaPanelApiClient;

$apiKey = 'your_api_key'; // get the api key from the settings aapanle
$baseUrl = 'https://example.com:8888'; // Replace with your aaPanel API base URL
$client = new aaPanelApiClient($apiKey, $baseUrl);
```


# Functionality
## Fetch Logs
### Fetches logs from the API.

```php
$logs = $client->fetchLogs();
var_dump($logs);
```
