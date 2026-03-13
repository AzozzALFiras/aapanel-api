# aaPanel API PHP Client

A comprehensive PHP client library for the [aaPanel](https://www.aapanel.com/) (BaoTa) API. Manage websites, databases, FTP, SSL certificates, files, cron jobs, firewall, DNS and more — all from PHP.

**350+ methods** across **27 modules** covering the full aaPanel API surface.

## Features

| Module | Types | Highlights |
|--------|-------|------------|
| **Websites** | PHP, Node.js, Python, Proxy | Create/delete/start/stop sites, domains, SSL, redirects, rewrite rules, backups, traffic limits, hotlink protection, password access, logs |
| **Databases** | MySQL, PostgreSQL, MongoDB, Redis, SQL Server | CRUD, passwords, permissions, backup/restore, sync, remote servers, table maintenance, binlog, slow logs, SSL |
| **FTP** | — | Accounts CRUD, password change, enable/disable, remarks |
| **Files** | — | Directory listing, read/write, upload/download, compress/extract, copy/move, permissions |
| **SSL** | — | Let's Encrypt, custom certificates, deploy, renew, close |
| **Cron** | — | Create/delete scheduled tasks, run immediately, logs, enable/disable |
| **Firewall** | — | Port rules, IP block/allow, SSH/ping toggle |
| **DNS** | — | A/AAAA/CNAME/MX/TXT records via dns_manager plugin |
| **System** | — | System stats, disk info, network, panel updates, logs, server config |

## Requirements

- PHP 7.2+ or 8.0+
- ext-curl
- ext-json

## Installation

```bash
composer require azozzalfiras/aapanel-api
```

## Quick Start

```php
require_once 'vendor/autoload.php';

use AzozzALFiras\AAPanelAPI\AaPanel;

$panel = new AaPanel('your_api_key', 'https://your-server:8888');

// Get system info
$info = $panel->system()->getSystemTotal();

// List PHP websites
$sites = $panel->website()->getList();

// Create a MySQL database
$panel->database()->create('mydb', 'myuser', 'mypass');
```

### Options

```php
$panel = new AaPanel('your_api_key', 'https://your-server:8888', [
    'timeout'    => 120,        // Request timeout in seconds (default: 60)
    'verify_ssl' => true,       // Verify SSL certificate (default: false)
    'cookie_dir' => '/tmp',     // Cookie storage directory (default: sys_get_temp_dir())
]);
```

---

## System

```php
// System statistics (OS, CPU, memory, uptime, panel version)
$panel->system()->getSystemTotal();

// Disk partition info
$panel->system()->getDiskInfo();

// Real-time CPU, memory, network, load
$panel->system()->getNetWork();

// Panel logs
$panel->system()->getLogs(20, 1);

// Check installation tasks
$panel->system()->getTaskCount();

// Check for / perform panel update
$panel->system()->updatePanel(true, false);

// Server configuration
$panel->system()->getConfig();
$panel->system()->setConfig(['param' => 'value']);
```

---

## Websites

The website module supports **4 project types**, each with its own class plus **shared features** (SSL, domains, redirects, backups, logs, etc.) available on all types.

### Access Patterns

```php
// Via facade (backward compatible — defaults to PHP)
$panel->website()->getList();
$panel->website()->create('example.com', '/www/wwwroot/example.com', 'My site');

// Via facade → specific type
$panel->website()->php()->getList();
$panel->website()->node()->getList();
$panel->website()->python()->getList();
$panel->website()->proxy()->getList();

// Direct shortcuts (skip facade)
$panel->phpSite()->getList();
$panel->nodeSite()->getList();
$panel->pythonSite()->getList();
$panel->proxySite()->getList();
```

### PHP Sites

```php
$php = $panel->website()->php();

// List / query
$php->getList(20, 1, 'example');
$php->getListV2(50, 1);                    // v2 endpoint
$php->getSiteTypes();                       // Get categories
$php->getPHPVersions();                     // Installed PHP versions

// Create
$php->create(
    'example.com',                          // Domain
    '/www/wwwroot/example.com',             // Root path
    'Production site',                      // Remarks
    ['www.example.com'],                    // Additional domains
    0,                                      // Category ID
    '81',                                   // PHP version
    '80',                                   // Port
    true, 'ftpuser', 'ftppass',            // Create FTP
    true, 'dbuser', 'dbpass'               // Create database
);

// Delete (with optional cleanup)
$php->delete(1, 'example.com', true, true, true);  // delete FTP + DB + files

// Start / Stop
$php->start(1, 'example.com');
$php->stop(1, 'example.com');

// PHP version management
$php->setPHPVersion('example.com', '82');
$php->getSitePHPVersion('example.com');

// Site categories
$php->addSiteType('E-commerce');
$php->setSiteType(1, 2);                   // Assign site to category
$php->removeSiteType(2);

// Default pages
$php->getDefaultSite();
$php->setDefaultSite('example.com');

// Deny file extensions
$php->getDenyAccess(1, 'example.com');
$php->setDenyAccess(1, 'example.com', 'sql,log,env');
```

### Node.js Projects

```php
$node = $panel->website()->node();

// Create project
$node->create(
    'my-app',                               // Project name
    '/www/node/my-app',                     // Project path
    'start',                                // npm script to run
    3000,                                   // Port
    'v18',                                  // Node version
    'www',                                  // User (www or root)
    'app.example.com',                      // Domain
    'My Node.js API'                        // Remarks
);

// Lifecycle
$node->start('my-app');
$node->stop('my-app');
$node->restart('my-app');
$node->delete('my-app');

// Info & monitoring
$node->getList();
$node->getProjectInfo('my-app');
$node->getProjectConfig('my-app');
$node->getRunState('my-app');
$node->getLoadInfo('my-app');

// Node.js versions
$node->isNodeInstalled();
$node->getNodeVersions();
$node->getProjectNodeVersion('my-app');
$node->setProjectNodeVersion('my-app', 'v20');

// Port & network
$node->setListenPort('my-app', 3001);
$node->checkPort(3001);
$node->bindExtranet('my-app');              // Enable Nginx/Apache mapping
$node->unbindExtranet('my-app');

// Domain management
$node->getProjectDomains('my-app');
$node->addProjectDomain('my-app', 'api.example.com');
$node->removeProjectDomain('my-app', 'api.example.com');

// NPM modules
$node->installPackages('my-app');           // npm install
$node->updatePackages('my-app');            // npm update
$node->reinstallPackages('my-app');         // rm node_modules + install
$node->getModules('my-app');               // List installed
$node->installModule('my-app', 'express');
$node->uninstallModule('my-app', 'express');
$node->upgradeModule('my-app', 'express');
$node->rebuildProject('my-app');            // npm rebuild
$node->getRunScripts('my-app');            // package.json scripts

// Config & logs
$node->setWebConfig('my-app');
$node->clearWebConfig('my-app');
$node->getProjectLog('my-app');
$node->getExecLogs('my-app');
$node->getSSLEndDate('my-app');
```

### Python Projects

```php
$py = $panel->website()->python();

// Create project
$py->create(
    'my-api',                               // Project name
    '/www/python/my-api',                   // Path
    'app.py',                               // Startup file
    8000,                                   // Port
    '3.11',                                 // Python version
    'fastapi',                              // Framework (flask/django/fastapi/general)
    'www',                                  // User
    'api.example.com',                      // Domain
    'gunicorn',                             // Start command
    'FastAPI Backend'                        // Remarks
);

// Lifecycle
$py->start('my-api');
$py->stop('my-api');
$py->restart('my-api');

// Python versions
$py->getPythonVersions();
$py->getProjectPythonVersion('my-api');
$py->setProjectPythonVersion('my-api', '3.12');

// Pip modules
$py->installPackages('my-api');             // pip install -r requirements.txt
$py->getModules('my-api');
$py->installModule('my-api', 'fastapi');
$py->uninstallModule('my-api', 'uvicorn');

// Network mapping
$py->bindExtranet('my-api');
$py->unbindExtranet('my-api');

// Domains & logs
$py->getProjectDomains('my-api');
$py->addProjectDomain('my-api', 'api.example.com');
$py->getProjectLog('my-api');
```

### Reverse Proxy Projects

```php
$proxy = $panel->website()->proxy();

// Create proxy
$proxy->create(
    'docker-app',                           // Project name
    'app.example.com',                      // Domain
    'http://127.0.0.1:8080',              // Backend target URL
    '/',                                    // Proxy directory
    '$host',                                // Send host header
    0,                                      // Cache (0=off, 1=on)
    1,                                      // WebSocket support
    'Docker container'                      // Remarks
);

// Proxy URL rules
$proxy->getProxyRules('docker-app');
$proxy->addProxyRule('docker-app', '/api/', 'http://127.0.0.1:9000');
$proxy->modifyProxyRule('docker-app', '/api/', ['target_url' => 'http://127.0.0.1:9001']);
$proxy->deleteProxyRule('docker-app', '/api/');

// Cache & compression
$proxy->setCache('docker-app', '/', true);
$proxy->clearCache('docker-app');
$proxy->setGzip('docker-app', true, 'text/html text/css application/json');

// Content replacement (e.g. http → https)
$proxy->setContentReplace('docker-app', [
    ['from' => 'http://', 'to' => 'https://'],
]);

// Custom headers
$proxy->setProxyHeaders('docker-app', '/', [
    'X-Real-IP'       => '$remote_addr',
    'X-Forwarded-For' => '$proxy_add_x_forwarded_for',
]);

// IP restrictions
$proxy->getIpRestrictions('docker-app');
$proxy->setIpBlacklist('docker-app', "192.168.1.100\n10.0.0.0/8");
$proxy->setIpWhitelist('docker-app', "192.168.1.0/24");

// Domains
$proxy->getProjectDomains('docker-app');
$proxy->addProjectDomain('docker-app', 'app2.example.com');
```

### Shared Features (All Website Types)

These methods are available on `php()`, `node()`, `python()`, and `proxy()`:

```php
$site = $panel->website()->php(); // or node(), python(), proxy()

// SSL / HTTPS
$site->getSSL('example.com');
$site->setSSL('example.com', $key, $cert);
$site->closeSSL(1, 'example.com');
$site->enableHttps('example.com');
$site->disableHttps('example.com');

// Domains
$site->getDomains(1);
$site->addDomain(1, 'example.com', 'www.example.com');
$site->deleteDomain(1, 'example.com', 'www.example.com');

// Redirects (301/302)
$site->getRedirects('example.com');
$site->createRedirect('example.com', 'domain', 'old.com', 'https://new.com', 301);
$site->deleteRedirect('example.com', 'redirect_name');

// Reverse proxy (site-level)
$site->getProxyList('example.com');
$site->createProxy('example.com', 'api-proxy', '/api/', 'http://127.0.0.1:3000');
$site->deleteProxy('example.com', 'api-proxy');

// Hotlink protection
$site->getHotlinkProtection(1, 'example.com');
$site->setHotlinkProtection(1, 'example.com', 'example.com,cdn.com', 'jpg,png,gif');

// Password access
$site->setPasswordAccess(1, 'admin', 'secret');
$site->closePasswordAccess(1);

// Traffic limits (Nginx)
$site->getTrafficLimit(1);
$site->setTrafficLimit(1, 300, 25, 512);    // perserver, perip, limit_rate
$site->closeTrafficLimit(1);

// Logs
$site->toggleAccessLogs(1);
$site->getErrorLog('example.com');
$site->getSecurityLog('example.com');

// Site config file
$site->getSiteConfigFile('example.com', 'nginx');
$site->saveSiteConfigFile('example.com', $configData, 'nginx');

// Rewrite rules / default document
$site->getRewriteList('example.com');
$site->getIndex(1);
$site->setIndex(1, 'index.php,index.html');

// Directory settings
$site->getSitePath(1);
$site->setPath(1, '/www/wwwroot/newpath');
$site->setRunPath(1, '/public');
$site->getDirUserINI(1, '/www/wwwroot/example.com');
$site->setDirUserINI('/www/wwwroot/example.com');

// Backups
$site->getBackups(1);
$site->createBackup(1);
$site->deleteBackup(1);

// Misc
$site->setRemarks(1, 'Production site');
$site->setExpiration(1, '2025-12-31');
$site->setDiskQuota(1, 5120);               // 5GB (XFS only)
```

---

## Databases

The database module supports **5 database engines**, each with its own class plus **shared features** (backup, sync, cloud servers).

### Access Patterns

```php
// Via facade (backward compatible — defaults to MySQL)
$panel->database()->create('mydb', 'myuser', 'mypass');
$panel->database()->getList();

// Via facade → specific type
$panel->database()->mysql()->getList();
$panel->database()->pgsql()->getList();
$panel->database()->mongodb()->getList();
$panel->database()->redis()->getList();
$panel->database()->sqlserver()->getList();
```

### MySQL

```php
$mysql = $panel->database()->mysql();

// CRUD
$mysql->create('mydb', 'myuser', 'pass123', 'utf8mb4', '127.0.0.1');
$mysql->delete(1, 'mydb');
$mysql->getList(20, 1, 'search_term');
$mysql->getInfo(1);
$mysql->getDatabaseSize([1, 2, 3]);

// Passwords
$mysql->setRootPassword('new_root_pass');
$mysql->setPassword(1, 'mydb', 'new_pass');

// Access permissions
$mysql->getAccess('mydb');
$mysql->setAccess('mydb', '%', true);        // All IPs + force SSL
$mysql->setAccess('mydb', '192.168.1.100');  // Specific IP

// Table maintenance
$mysql->repairTable('mydb');
$mysql->optimizeTable('mydb');
$mysql->convertEngine('mydb', 'users', 'InnoDB');

// Server configuration
$mysql->getMySQLInfo();                      // Version, datadir, port
$mysql->getDbStatus();                       // Config parameters
$mysql->setDbConf(['max_connections' => 500, 'key_buffer_size' => '128M']);
$mysql->getRunStatus();                      // Runtime stats
$mysql->setPort(3307);
$mysql->setDataDir('/data/mysql');

// Binary logs
$mysql->binLog();                            // Get status
$mysql->getBinlogs();                        // List files
$mysql->clearBinlogs(7);                     // Purge older than 7 days

// Error & slow logs
$mysql->getErrorLog();
$mysql->getErrorLog(true);                   // Clear log
$mysql->getSlowLogs();

// SSL
$mysql->checkSslStatus();
$mysql->enableSsl();

// Users
$mysql->getMysqlUser();
```

### PostgreSQL

```php
$pg = $panel->database()->pgsql();

$pg->create('pgdb', 'pguser', 'pgpass', 'UTF8');
$pg->delete(1, 'pgdb');
$pg->getList();
$pg->setPassword(1, 'pgdb', 'new_pass');
$pg->getRootPassword();
$pg->setRootPassword('new_postgres_pass');
$pg->getOptions();                           // Port, listen_addresses
```

### MongoDB

```php
$mongo = $panel->database()->mongodb();

$mongo->create('mgdb', 'mgpass');
$mongo->delete(1, 'mgdb');
$mongo->getList();
$mongo->setPassword(1, 'mgdb', 'new_pass');
$mongo->getRootPassword();
$mongo->setAuthStatus(true, 'admin_pass');   // Enable authentication
$mongo->setAuthStatus(false);                // Disable authentication
$mongo->exists('mgdb');                      // Check if DB exists
```

### Redis

```php
$redis = $panel->database()->redis();

// Database list
$redis->getList();

// Key operations
$redis->setKey(0, 'user:1', '{"name":"John"}', 'string', 3600);  // DB0, TTL 1hr
$redis->setKey(0, 'scores', '100', 'zset');
$redis->getKeys(0, 1, 50, 'user:*');        // Search keys in DB0
$redis->deleteKey(0, 'user:1');

// Clear databases
$redis->clearDatabase(0);                    // FLUSHDB (DB0 only)
$redis->clearDatabase('all');                // FLUSHALL (all DBs)

// Backup
$redis->getBackupList();
$redis->createBackup();

// Config
$redis->getOptions();
```

### SQL Server

```php
$mssql = $panel->database()->sqlserver();

$mssql->create('msdb', 'msuser', 'mspass');
$mssql->delete(1, 'msdb');
$mssql->getList();
$mssql->setPassword(1, 'msdb', 'new_pass');
$mssql->getRootPassword();                   // SA password
$mssql->setRootPassword('new_sa_pass');
$mssql->getDatabaseSize(1);
```

### Shared Database Features (All Types)

```php
$db = $panel->database()->mysql(); // or pgsql(), mongodb(), redis(), sqlserver()

// Backup & restore
$db->createBackup(1);
$db->deleteBackup(1);
$db->importFile('mydb', '/www/backup/mydb.sql.gz');

// Sync
$db->syncToDatabases(1);                    // Push all to server
$db->syncGetDatabases(0);                   // Pull from local server

// Remote servers
$db->addCloudServer('db.remote.com', 3306, 'root', 'pass', 'Production DB');
$db->getCloudServers();
$db->modifyCloudServer(1, 'db.remote.com', 3306, 'root', 'newpass');
$db->removeCloudServer(1);
$db->checkCloudStatus(['host' => 'db.remote.com', 'port' => 3306]);

// Remarks
$db->setRemarks(1, 'Production database');
```

---

## FTP

```php
$ftp = $panel->ftp();

$ftp->getList(20, 1, 'search');
$ftp->create('ftpuser', 'ftppass', '/www/wwwroot/mysite', 'My FTP');
$ftp->delete(1, 'ftpuser');
$ftp->getDetails('ftpuser');
$ftp->setPassword(1, 'ftpuser', 'newpass');
$ftp->setStatus(1, 'ftpuser', true);        // Enable
$ftp->setRemarks(1, 'Deploy account');
```

---

## File Manager

```php
$files = $panel->files();

// Directory & file operations
$files->getDirectory('/www/wwwroot', 1, 100);
$files->getFileBody('/www/wwwroot/index.html');
$files->saveFileBody('/www/wwwroot/index.html', '<h1>Hello</h1>');
$files->createFile('/www/wwwroot/newfile.txt');
$files->createDirectory('/www/wwwroot/newdir');
$files->deleteFile('/www/wwwroot/oldfile.txt');
$files->deleteDirectory('/www/wwwroot/olddir');

// Copy / Move / Rename
$files->copy('/www/source.txt', '/www/dest.txt');
$files->move('/www/old.txt', '/www/new.txt');

// Permissions
$files->setPermissions('/www/wwwroot/mysite', '755', true);

// Compress / Extract
$files->compress('/www/wwwroot/mysite', '/www/backup/mysite.zip');
$files->extract('/www/backup/mysite.zip', '/www/wwwroot/restored');

// Upload / Download
$files->upload('/local/file.zip', '/www/wwwroot/', 'file.zip');
$files->downloadRemoteFile('https://example.com/file.tar.gz', '/www/', 'file.tar.gz');

// Size
$files->getSize('/www/wwwroot/mysite');
```

---

## SSL Certificates

```php
$ssl = $panel->ssl();

// Get SSL info
$ssl->getSSL('example.com');

// Apply Let's Encrypt certificate
$ssl->applyCertificate('example.com', 1, 'http');

// Deploy custom certificate
$ssl->setSSL('example.com', $privateKey, $certificate);

// Apply + deploy in one step
$ssl->applyAndDeploy('example.com', 1);

// Renew
$ssl->renewCertificate($index);
$ssl->renewByDomain('example.com');          // Auto-fetches index

// Remove SSL
$ssl->closeSSL(1, 'example.com');
```

---

## Cron Jobs

```php
$cron = $panel->cron();

// List
$cron->getList();

// Create shell script cron (daily at 3:00)
$cron->create('Daily Cleanup', 'day', '3', '0', 'rm -rf /tmp/cache/*', 'toShell');

// Create database backup cron
$cron->create('DB Backup', 'day', '2', '0', '', 'database', 'localhost', 'mydb', 5);

// Delete / run / logs
$cron->delete(1);
$cron->startTask(1);                         // Run immediately
$cron->getLogs(1);

// Enable / disable
$cron->setStatus(1, false);                  // Disable
$cron->setStatus(1, true);                   // Enable
```

---

## Firewall

```php
$fw = $panel->firewall();

// Rules
$fw->getList();
$fw->addPortRule('8080', 'accept', 'Custom app');
$fw->deletePortRule(1);
$fw->addIpRule('192.168.1.100', 'drop', 'Blocked IP');

// Global toggles
$fw->setStatus(true);                        // Enable firewall
$fw->setSshStatus(false);                    // Disable SSH
$fw->setPingStatus(true);                    // Enable ping
```

---

## DNS

Requires the `dns_manager` plugin installed on the panel.

```php
$dns = $panel->dns();

// A record
$dns->addRecord('www', 'example.com', '1.2.3.4', 'A', 600);

// CNAME
$dns->addRecord('cdn', 'example.com', 'cdn.provider.com', 'CNAME');

// MX record
$dns->addRecord('@', 'example.com', 'mail.example.com', 'MX');

// TXT record (SPF, DKIM, etc.)
$dns->addRecord('@', 'example.com', 'v=spf1 include:_spf.google.com ~all', 'TXT');

// Modify / Delete
$dns->modifyRecord('www', 'example.com', '5.6.7.8');
$dns->deleteRecord('www', 'example.com', '1.2.3.4');
```

---

## Error Handling

```php
use AzozzALFiras\AAPanelAPI\AaPanel;
use AzozzALFiras\AAPanelAPI\Exceptions\AaPanelException;
use AzozzALFiras\AAPanelAPI\Exceptions\ConnectionException;
use AzozzALFiras\AAPanelAPI\Exceptions\AuthenticationException;

try {
    $panel = new AaPanel('your_key', 'https://your-server:8888');
    $sites = $panel->website()->getList();
} catch (AuthenticationException $e) {
    // Invalid API key or IP not in whitelist
    echo "Auth failed: " . $e->getMessage();
} catch (ConnectionException $e) {
    // cURL error, invalid JSON response, network issue
    echo "Connection error: " . $e->getMessage();
} catch (AaPanelException $e) {
    // General API error
    echo "Error: " . $e->getMessage();
}
```

---

## Backward Compatibility

The legacy `aaPanelApiClient` class is still available and works exactly as before:

```php
use AzozzALFiras\AAPanelAPI\aaPanelApiClient;

$client = new aaPanelApiClient('your_key', 'https://your-server:8888');
$client->fetchLogs();
$client->addSite('example.com', 'example', 'My site');
$client->fetchSites(20, 1);
// ... all old methods still work
```

To migrate, switch to the new `AaPanel` class — all methods map 1:1.

---

## Project Structure

```
src/
├── AaPanel.php                     # Main entry point
├── HttpClient.php                  # HTTP layer with error handling
├── aaPanelApiClient.php            # Legacy backward-compatible wrapper
├── Exceptions/
│   ├── AaPanelException.php
│   ├── AuthenticationException.php
│   └── ConnectionException.php
└── Modules/
    ├── System.php                  # System stats, logs, panel config
    ├── Website.php                 # Website facade (→ Websites/)
    ├── Database.php                # Database facade (→ Databases/)
    ├── Ftp.php                     # FTP account management
    ├── FileManager.php             # File operations
    ├── Ssl.php                     # SSL certificate management
    ├── Cron.php                    # Scheduled tasks
    ├── Firewall.php                # Firewall & security
    ├── Dns.php                     # DNS records (plugin)
    ├── Websites/
    │   ├── SiteCommon.php          # Shared trait (SSL, domains, logs, ...)
    │   ├── PhpSite.php             # PHP websites
    │   ├── NodeSite.php            # Node.js projects (32+ endpoints)
    │   ├── PythonSite.php          # Python projects (uWSGI/Gunicorn)
    │   └── ProxySite.php           # Reverse proxy projects
    └── Databases/
        ├── DatabaseCommon.php      # Shared trait (backup, sync, cloud)
        ├── Mysql.php               # MySQL (maintenance, binlog, SSL)
        ├── PostgreSql.php          # PostgreSQL
        ├── MongoDb.php             # MongoDB
        ├── Redis.php               # Redis (key CRUD, DB0-15)
        └── SqlServer.php           # SQL Server (MSSQL)
```

## License

This project is licensed under the MIT License.

Copyright (c) 2024 Azozz ALFiras
