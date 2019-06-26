#  IPFinder CLI (Command Line Interface) https://ipfinder.io/
-  Supports Single IP Address, asn, ranges, firewall as Input
-  Supports Bulk
-  Exports Results to Screen or to An Output File
-  Supports IPv4 and IPv6
-  Supports ASN number , RANGES , Firewall

## Getting Started
sign up  for a free account at [https://ipfinder.io/auth/signup](https://ipfinder.io/auth/signup), for Free IPFinder API access token.

The free plan is limited to 4,000 requests a day, and doesn't include some of the data fields
To enable all the data fields and additional request volumes see [https://ipfinder.io/pricing](https://ipfinder.io/pricing).

## Documentation

Visit [IPFinder documentation](https://ipfinder.io/docs).

## System Requirements
-  PHP >= 7.0
-  JSON PHP Extension
-  CURL PHP Extension
-  [official PHP library for IPfinder](https://github.com/ipfinder-io/ip-finder-php).

## Installation
### Via composer
First, download the IPfinder cli using Composer:
```php
composer global require ipfinder-io/ip-finder-cli
```
Make sure to place composer's system-wide vendor bin directory in your `$PATH` so the IPfinder executable can be located by your system. This directory exists in different locations based on your operating system; however, some common locations include:
- macOS: `$HOME/.composer/vendor/bin`
- GNU / Linux Distributions: `$HOME/.config/composer/vendor/bin`
- Windows: `%USERPROFILE%\AppData\Roaming\Composer\vendor\bin`
### Linux Distributions / macOS
download the IPfinder cli using from github using curl
```bash
$ brew tap ipfinder-io/ip-finder-cli
$ brew install ip-fidner-cli
## using curl

$ curl -LO https://github.com/ipfinder-io/ip-finder-cli/releases/download/v1.0.2/ipfinder.phar
## using wget
$ wget https://github.com/ipfinder-io/ip-finder-cli/releases/download/v1.0.2/ipfinder.phar
$ chmod +x ipfinder.phar
$ sudo mv ipfinder.phar /usr/bin/ipfinder
$ ipfinder -h
```
## Windows
1.  Download [IPFINDER PHAR](https://github.com/ipfinder-io/ip-finder-cli/releases/download/v1.0.2/ipfinder.phar) from github
2.  Create a directory for PHP binaries; e.g., `C:\bin`
3.  Open a command line (e.g., press **Windows+R** » type `cmd` » ENTER)
4.  Create a wrapping batch script (results in `C:\bin\ipfinder.cmd`):
```bash
C:\Users\username> cd C:\bin
C:\bin> echo @php "%~dp0ipfinder.phar" %* > ipfinder.cmd
C:\bin> exit
```
5. Open a new command line and confirm that you can execute IPfinder from any path:
```bash
C:\Users\username> ipfinder --help
````
### Command list

| shortopts   | longopts     | Description
| ----------- | -----------  |-----------
| `-h`      | `--help`    | This help text
| `-o`      | `--output`  |  save to a given file
| `-u`      | `--update`  | App Code update
| `-a`      | `--auth`  |  lookup your IP address information.
| `-m`      | `--format`  | fetching IP address information.
| `-i`      | `--ip`  | firewall  supported format https://ipfinder.io/docs/?shell#firewall.
| `-n`      | `--asn`  | fetching AS number information.
| `-r`      | `--ranges`  | fetching IP Address Ranges information.
| `-f`      | `--firewall`  | fetching firewall information
| `-d`      | `--domain`  | Get information for Domain IP.
| `-d`      | `--dhistory`  | Get information for Domain IP history.
| `-dl`     | `--dlist`  | Get information for list Domain By ASN, Country,Ranges.
| `-s`      | `--status`  |  Get information for your token.
| `-c`      | `--config`  | Add your Token and labg
| `-l`      | `--shell`  | Run interactively


## License
Licensed under the [Apache-2.0](https://github.com/ipfinder-io/ip-finder-cli/blob/master/LICENSE).
## Support
Contact Us With Additional Questions About Our API, if you would like more information about our API that isn’t available in our IP geolocation API developer documentation, simply [contact](https://ipfinder.io/contact) us at any time and we’ll be able to help you find what you need..
