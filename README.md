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

> Before starting the secure WebSocket server for the first time, make sure to have created the `certificate.crt` and `private.key` files into the `ssl` folder.

This is how to run the Docker container:

```
docker compose up -d
```

Alternatively, you may want to start the chess server manually:

```
php cli/wss-server.php
```
```
Welcome to PHP Chess Server
Commands available:
/accept {"jwt":"<string>"} Accepts a request to play a game.
/draw {"action":["accept","decline","propose"]} Allows to offer a draw.
/heuristics {"movetext":"<string>"} Takes a balanced heuristic picture of the given PGN movetext.
/heuristics_bar {"fen":"<string>","variant":"<string>"} Takes an expanded heuristic picture of the current position.
/inbox {"action":["create","read","reply"],"variant":["960","capablanca","classical"],"settings":{"fen":"<string>","movetext":"<string>","startPos":"<string>"},"hash":"<string>","movetext":"<string>"} Correspondence chess.
/leave {"action":["accept"]} Allows to leave a game.
/legal {"position":"<string>"} Returns the legal FEN positions of a piece.
/online_games Returns the online games waiting to be accepted.
/play_lan {"color":"<string>","lan":"<string>"} Plays a chess move in long algebraic notation.
/randomizer {"turn":"<string>","items":"<string>"} Starts a random position.
/rematch {"action":["accept","decline","propose"]} Allows to offer a rematch.
/resign {"action":["accept"]} Allows to resign a game.
/restart {"hash":"<string>"} Restarts a game.
/start {"variant":["960","capablanca","classical"],"mode":["fen","san","rav","play","stockfish"],"settings":{"color":["w","b"],"fen":"<string>","movetext":"<string>","settings":"<string>","startPos":"<string>"}} Starts a new game.
/stockfish {"options":{"Skill Level":"int"},"params":{"depth":"int"}} Returns Stockfish's response to the current position.
/takeback {"action":["accept","decline","propose"]} Allows to manage a takeback.
/undo Undoes the last move.

Listening to commands...
```

Open a console in your favorite browser and run the following commands:

```
const ws = new WebSocket('wss://pchess.net:8443');
ws.onmessage = (res) => { console.log(res.data) };
ws.send('/start classical analysis');
```

### License

[The GNU General Public License](https://github.com/chesslablab/chess-server/blob/master/LICENSE).

### Contributions

See the [contributing guidelines](https://github.com/chesslablab/chess-server/blob/master/CONTRIBUTING.md).

Happy learning and coding! Thank you, and keep up the hard work!
