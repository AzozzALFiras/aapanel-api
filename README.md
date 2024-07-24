# aaPanel API PHP Client

A PHP client library for interacting with the aaPanel API.


# Features
- Fetch logs from the API.
- Add a new site.
- Add a subdomain.
- Delete a subdomain.
- Fetch list of FTP accounts.
- Add a new FTP account.
- Delete an FTP account.
- Import SQL file into a database.
- Save file content to a specified path.
- Unzip a ZIP archive to a specified destination.
- Apply SSL certificate to a domain.
- Renew SSL certificate for a domain.
- Get SSL details for a domain and return the 'index' value.
- Enable HTTPS redirection for a site.
- Disable a site.
- Enable a site.
- Retrieve details of a specific FTP account.
- Set server configuration parameters.
- Get server configuration parameters.


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


## Add a New Site
### Adds a new site to aaPanel.

```php
$response = $client->addSite('example.com', 'example', 'Example site');
var_dump($response);
```


## Add a Subdomain
### Adds a subdomain to an existing domain.

```php
$response = $client->addSubdomain('subdomain', 'example.com', '192.168.1.1');
var_dump($response);
```


## Delete a Subdomain
### Deletes a subdomain from an existing domain.

```php
$response = $client->deleteSubdomain('subdomain', 'example.com', '192.168.1.1');
var_dump($response);
```


## Fetch FTP Accounts
### Fetches a list of FTP accounts.

```php
$ftpAccounts = $client->fetchFtpAccounts();
var_dump($ftpAccounts);
```


## Add FTP Account
### Adds a new FTP account.

```php
$response = $client->addFtpAccount('ftpuser', 'ftppassword');
var_dump($response);
```


## Delete FTP Account
### Deletes an FTP account.

```php
$response = $client->deleteFtpAccount('ftpuser');
var_dump($response);
```


## Import SQL File
### Imports an SQL file into a database.

```php
$response = $client->importSqlFile('/path/to/example.sql', 'example_database');
var_dump($response);
```


## Save File
### Saves file content to a specified path.

```php
$fileContent = 'Hello, world!';
$response = $client->saveFile($fileContent, '/path/to/file.txt');
var_dump($response);
```


## Unzip File
### Unzips a ZIP archive to a specified destination.

```php
$response = $client->unzipFile('/path/to/example.zip', '/path/to/extracted', 'zip_password');
var_dump($response);
```


## Apply SSL Certificate
### Applies an SSL certificate to a domain.

```php
$response = $client->applySslCertificate('example.com', 1, 0);
var_dump($response);
```

## Renew SSL Certificate
### Renews an SSL certificate for a domain.

```php
$response = $client->renewCert('example.com');
var_dump($response);
```


## Get SSL Details
### Gets SSL details for a domain.

```php
$index = $client->getIndexValue('example.com');
var_dump($index);
```

## Enable HTTPS Redirection
### Enables HTTPS redirection for a site.

```php
$response = $client->enableHttpsRedirection('example.com');
var_dump($response);
```

## Disable Site
### Disables a site.

```php
$response = $client->disableSite(1, 'example.com');
var_dump($response);
```


## Enable Site
### Enables a site.

```php
$response = $client->enableSite(1, 'example.com');
var_dump($response);
```


## Get FTP Account Details
### Retrieves details of a specific FTP account.

```php
$details = $client->getFtpAccountDetails('ftpuser');
var_dump($details);
```


## Set Server Config
### Sets server configuration parameters.

```php
$config = [
    'param1' => 'value1',
    'param2' => 'value2'
];
$response = $client->setServerConfig($config);
var_dump($response);
```


## Get Server Config
### Gets server configuration parameters.

```php
$config = $client->getServerConfig();
var_dump($config);
```


# License
## This project is licensed under the MIT License - see the LICENSE file for details.


# MIT License
### Copyright (c) 2024 Azozz ALFiras

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.




