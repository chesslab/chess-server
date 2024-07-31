# Installation

Clone the `chesslablab/chess-server` repo into your projects folder.

Then `cd` the `chess-server` directory and create an `.env` file:

```txt
cp .env.example .env
```

Before starting the secure WebSocket server for the first time, make sure to have installed the `fullchain.pem` and `privkey.pem` files in the `ssl` folder.

Run the Docker container in detached mode in the background:

```txt
docker compose -f docker-compose.wss.yml up -d
```

Finally, if you are running the chess server on your localhost along with the [website](https://github.com/chesslablab/website), you may want to add an entry to your `/etc/hosts` file as per the `WEBSOCKET` variable defined in the [assets/env.example.js](https://github.com/chesslablab/website/blob/main/assets/env.example.js) file.

```txt
127.0.0.1       async.chesslablab.org
```
