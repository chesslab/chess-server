# Installation

## Requirements

- PHP >= 8.1
- Stockfish >= 15.1

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
| [cli/workerman/wss.php](https://github.com/chesslablab/chess-server/blob/master/cli/workerman/wss.php) | Secure WebSocket. |

Alternatively, it can use Ratchet WebSockets.

| Script | Description |
| ------ | ----------- |
| [cli/ratchet/wss.php](https://github.com/chesslablab/chess-server/blob/master/cli/ratchet/wss.php) | Secure WebSocket. |


Before starting the secure WebSocket server for the first time, make sure to have created the `fullchain.pem` and `privkey.pem` files in the `ssl` folder.

```txt
php cli/workerman/wss.php start -d
```

This will allow the `WSS_ALLOWED_HOST` defined in the `.env` file to send requests to the chess server.

## Run the Chess Server on a Docker Container

The chess server can also run on a Docker container.

```txt
docker compose -f docker-compose.wss.yml up -d
```
