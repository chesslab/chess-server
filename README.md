## PHP Chess Server

PHP Ratchet WebSocket server using [PHP Chess](https://github.com/chesslablab/php-chess).

### Documentation

Read the latest docs [here](https://php-chess-server.readthedocs.io/en/latest/).

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

Finally, you may want to add the following entry to your `/etc/hosts` file if running the PHP chess server on your localhost along with [React Chess](https://github.com/chesslablab/react-chess) as per the `REACT_APP_WS_HOST` variable defined in the [react-chess/.env.example](https://github.com/chesslablab/react-chess/blob/master/.env.example) file.

```
127.0.0.1       pchess.net
```

### Run the Chess Server

The chess server comes in four different flavors and can be run using a PHP script.

| Script | Description | Use |
| ------ | ----------- | --- |
| [cli/testing.php](https://github.com/chesslablab/chess-server/blob/master/cli/testing.php) | TCP socket. | Functional testing. |
| [cli/dev.php](https://github.com/chesslablab/chess-server/blob/master/cli/dev.php) | Simple WebSocket server. | Development. |
| [cli/staging.php](https://github.com/chesslablab/chess-server/blob/master/cli/staging.php) | Secure WebSocket server. | Staging. |
| [cli/prod.php](https://github.com/chesslablab/chess-server/blob/master/cli/prod.php) | Secure WebSocket server. | Production. |


#### Functional Testing

Run the TCP socket server.

```
php cli/testing.php
```

#### Simple WebSocket Server

Run the simple WebSocket server if you are not using an SSL/TLS certificate.

```
php cli/dev.php
```

#### Staging

> Before starting the secure WebSocket server for the first time, make sure to have created the `certificate.crt` and `private.key` files into the `ssl` folder.

Run the staging secure WebSocket server if you don't want to check the website's origin.

```
php cli/staging.php
```

This will allow any origin to send a request to it.

#### Production

> Before starting the secure WebSocket server for the first time, make sure to have created the `certificate.crt` and `private.key` files into the `ssl` folder.

Run the secure WebSocket server to check the website's origin as defined in the `WSS_ALLOWED` variable in the `.env.example` file.

```
php cli/prod.php
```

This will allow the `WSS_ALLOWED` website to send a request to it.

### Run the Chess Server on a Docker Container

Alternatively, the chess server can run on a Docker container.

#### Functional Testing

```
docker compose -f docker-compose.testing.yml up -d
```

#### Development

```
docker compose -f docker-compose.dev.yml up -d
```

#### Staging

```
docker compose -f docker-compose.staging.yml up -d
```

#### Production

```
docker compose -f docker-compose.prod.yml up -d
```

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
