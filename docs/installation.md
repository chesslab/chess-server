# Installation

## Requirements

- PHP >= 8.1

You may want to optionally install Stockfish >= 15.1 as it is described in [Play Computer](https://php-chess.docs.chesslablab.org/play-chess/#play-computer).

## Setup

Clone the `chesslablab/chess-server` repo into your projects folder as it is described in the following example:

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

The chess server runs by default using Ratchet-PHP WebSockets and comes in four different flavors.

| Script | Description | Use |
| ------ | ----------- | --- |
| [cli/ratchet/testing.php](https://github.com/chesslablab/chess-server/blob/master/cli/ratchet/testing.php) | TCP socket. | Functional testing. |
| [cli/ratchet/dev.php](https://github.com/chesslablab/chess-server/blob/master/cli/ratchet/dev.php) | Simple WebSocket server. | Development. |
| [cli/ratchet/staging.php](https://github.com/chesslablab/chess-server/blob/master/cli/ratchet/staging.php) | Secure WebSocket server. | Staging. |
| [cli/ratchet/prod.php](https://github.com/chesslablab/chess-server/blob/master/cli/ratchet/prod.php) | Secure WebSocket server. | Production. |


### Functional Testing

Run the TCP socket server.

```
php cli/ratchet/testing.php
```

### Simple WebSocket Server

Run the simple WebSocket server if you are not using an SSL/TLS certificate.

```txt
php cli/ratchet/dev.php
```

### Staging

Before starting the secure WebSocket server for the first time, make sure to have created the `fullchain.pem` and `privkey.pem` files into the `ssl` folder.

Run the staging secure WebSocket server if you don't want to check the website's origin.

```txt
php cli/ratchet/staging.php
```

This will allow any origin to send a request to it.

### Production

Before starting the secure WebSocket server for the first time, make sure to have created the `fullchain.pem` and `privkey.pem` files into the `ssl` folder.

Run the secure WebSocket server to check the website's origin as defined in the `WSS_ALLOWED` variable in the `.env.example` file.

```txt
php cli/ratchet/prod.php
```

This will allow the `WSS_ALLOWED` website to send a request to it.

## Run the Chess Server on a Docker Container

Alternatively, the chess server can run on a Docker container.

### Functional Testing

```txt
docker compose -f docker-compose.testing.yml up -d
```

### Development

```txt
docker compose -f docker-compose.dev.yml up -d
```

### Staging

```txt
docker compose -f docker-compose.staging.yml up -d
```

### Production

```txt
docker compose -f docker-compose.prod.yml up -d
```
