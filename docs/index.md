# PHP Chess Server

✨ [PHP Chess Server](https://github.com/chesslablab/chess-server) is an asynchronous PHP server that provides chess services over a WebSocket connection.

| Port | Service Name | Description |
| ---- | ------------ | ----------- |
| 9443 | data | JSON-formatted data |
| 8443 | game | Chess functionality |
| 7443 | binary | Binary data |
| 6443 | auth | Authentication functionality |

## Installation

Clone the `chesslablab/chess-server` repo into your projects folder. Then `cd` the `chess-server` directory and create an `.env` file:

```txt
cp .env.example .env
```

Create empty log files for each service.

```txt
touch storage/data.log
touch storage/game.log
touch storage/binary.log
touch storage/auth.log
```

Make sure to read the [SSL Certificate Setup](https://chesslablab.github.io/website/#ssl-certificate-setup) section. Install the `fullchain.pem` and `privkey.pem` files in the `ssl` folder, and run the Docker container in detached mode in the background:

```txt
docker compose -f docker-compose.workerman.yml up -d
```

Finally, if you are running the chess server in a local development environment along with the [website](https://github.com/chesslablab/website), you may want to add a domain name entry to your `/etc/hosts` file as per the `WEBSOCKET_SERVER` variable defined in the [assets/env.example.js](https://github.com/chesslablab/website/blob/main/assets/env.example.js) file.

```txt
127.0.0.1       async.chesslablab.org
```

## Features

### Object-Oriented

The socket, the chess commands, the game modes and the asynchronous tasks are all implemented using OOP principles.

### Flexible

The flexible architecture of PHP Chess Server allows support for multiple async PHP frameworks, with the default one being Workerman.

- Workerman
- Ratchet

The Spatie async library providing a wrapper around PHP's PCNTL extension is used in order for asynchronous commands to not block the event loop.
