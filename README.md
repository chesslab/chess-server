## PHP Chess Server

PHP Ratchet WebSocket server using [PHP Chess](https://github.com/chesslablab/php-chess).

### Documentation

Read the latest docs [here](https://php-chess-server.readthedocs.io/en/latest/).

### Demo

Check out [this demo](https://www.chesslablab.com).

### Setup

Clone the `chesslablab/chess-server` repo into your projects folder as it is described in the following example:

```
git clone git@github.com:chesslablab/chess-server.git
```

Then `cd` the `chess-server` directory and install the Composer dependencies:
```
composer install
```

Create an `.env` file:
```
cp .env.example .env
```

Add the following entry to your `/etc/hosts` file:

```
127.0.0.1       pchess.net
```

### Run the Chess Server

The chess server comes in four different flavors.

| Script | Description | Use |
| ------ | ----------- | --- |
| cli/tcp.php | TCP socket. | Functional testing. |
| cli/ws.php | Simple WebSocket server. | Development. |
| cli/wss-staging.php | Secure WebSocket server. | Staging. |
| cli/wss.php | Secure WebSocket server. | Production. |

#### Functional Testing

Run the TCP socket server.

```
php cli/tcp.php
```

Run the functional tests.

```
vendor/bin/phpunit tests/functional
PHPUnit 9.6.11 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.2.9
Configuration: /home/standard/projects/chess-server/phpunit.xml

..                                                                  2 / 2 (100%)

Time: 00:00.042, Memory: 8.00 MB

OK (2 tests, 2 assertions)
```

#### Simple WebSocket Server

Run the simple WebSocket server if you are not using an SSL/TLS certificate.

```
php cli/ws.php
```

#### Staging Secure WebSocket Server

Before starting the secure WebSocket server for the first time, make sure to have created the `certificate.crt` and `private.key` files into the `ssl` folder.

Run the staging secure WebSocket server if you don't want to check the website's origin.

```
php cli/wss-staging.php
```

This will allow any origin to send a request to it.

#### Production Secure WebSocket Server

Before starting the secure WebSocket server for the first time, make sure to have created the `certificate.crt` and `private.key` files into the `ssl` folder.

Run the secure WebSocket server to check the website's origin as defined in the `WSS_ALLOWED` variable in the `.env.example` file.

```
php cli/wss.php
```

This will allow the `WSS_ALLOWED` website to send a request to it.

### Connect to the Secure WebSocket Server

Open a console in your favorite browser and run the following commands:

```
const ws = new WebSocket('wss://pchess.net:8443');
ws.send('/start classical fen');
```

### License

[The GNU General Public License](https://github.com/chesslablab/chess-server/blob/master/LICENSE).

### Contributions

See the [contributing guidelines](https://github.com/chesslablab/chess-server/blob/master/CONTRIBUTING.md).

Happy learning and coding!
