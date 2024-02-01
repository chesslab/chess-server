# Installation

## Requirements

- PHP >= 8.1

You may want to optionally install Stockfish >= 15.1 as it is described in [Play Computer](https://php-chess.docs.chesslablab.org/play-chess/#play-computer).

## Setup

Clone the `chesslablab/chess-server` repo into your projects folder:

```txt
git clone git@github.com:chesslablab/chess-server.git
```

Then `cd` the `chess-server` directory and install the Composer dependencies:

```txt
composer install
```

Create an `.env` file:

```txt
cp .env.example .env
```

Finally, you may want to add the following entry to your `/etc/hosts` file if running the PHP chess server on your localhost along with [React Chess](https://github.com/chesslablab/react-chess) as per the `REACT_APP_WS_HOST` variable defined in the [react-chess/.env.example](https://github.com/chesslablab/react-chess/blob/master/.env.example) file.

```txt
127.0.0.1       async.chesslablab.org
```

## Run the Chess Server

PHP Chess Server uses Workerman WebSockets.

| Script | Description |
| ------ | ----------- |
| [cli/workerman/tcp.php](https://github.com/chesslablab/chess-server/blob/master/cli/workerman/tcp.php) | TCP socket. |
| [cli/workerman/wss.php](https://github.com/chesslablab/chess-server/blob/master/cli/workerman/wss.php) | Secure WebSocket. |

Alternatively, it can use Ratchet WebSockets.

| Script | Description |
| ------ | ----------- |
| [cli/ratchet/tcp.php](https://github.com/chesslablab/chess-server/blob/master/cli/ratchet/tcp.php) | TCP socket. |
| [cli/ratchet/wss.php](https://github.com/chesslablab/chess-server/blob/master/cli/ratchet/wss.php) | Secure WebSocket. |


### TCP Socket

It is recommended to run the TCP socket server for testing purposes.

```
php cli/workerman/tcp.php
```

### Secure WebSocket

Before starting the secure WebSocket server for the first time, make sure to have created the `fullchain.pem` and `privkey.pem` files in the `ssl` folder.

```txt
php cli/workerman/wss.php
```

This will allow the `WSS_ALLOWED_HOST` defined in the `.env` file to send requests to it.

## Run the Chess Server on a Docker Container

Alternatively, the chess server can run on a Docker container.

### TCP Socket

```txt
docker compose -f docker-compose.tcp.yml up -d
```

### Secure WebSocket

```txt
docker compose -f docker-compose.wss.yml up -d
```
