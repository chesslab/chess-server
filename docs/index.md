# Features

Similar to the [Chess API](https://chess-api.readthedocs.io/en/latest/), the [Chess Server](https://github.com/chesslablab/chess-server) provides functionality to play chess online. Also it can be hosted on a custom domain. The main difference between both is that the Chess API endpoints may take few seconds to execute like a file download or a database query, while the Chess Server commands are intended to run faster.

This is how to open a WebSocket connection in JavaScript.

```js
const ws = new WebSocket('wss://chesslablab.net:8443');
```

That's it!

Now you're set up to start playing chess.

```js
ws.send('/start classical fen');
```

The `/start` command above starts a new classical chess game and retrieves a JSON response from the server.

```text
{
  "/start": {
    "variant": "classical",
    "mode": "fen",
    "fen": "rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq -"
  }
}
```

On successful server response a FEN string representing the starting position is returned as well as the chess variant and the game mode. This is the classical starting position in FEN format.

Now you're ready to make your first move.

What about 1.e4?

This is the so-called King's Pawn Game, one of the most popular chess openings, in Portable Game Notation (PGN) format. Humans can understand chess games in PGN easily but this format is not that great for computers as well as for graphic user interfaces (GUI) which may prefer the Long Algebraic Notation (LAN) format instead.

Let's play 1.e4 in LAN format.

```js
ws.send('/play_lan w e2e4');
```

The `/play_lan` command above retrieves the following JSON response.

```text
{
  "/play_lan": {
    "turn": "b",
    "pgn": "e4",
    "castlingAbility": "KQkq",
    "movetext": "1.e4",
    "fen": "rnbqkbnr/pppppppp/8/8/4P3/8/PPPP1PPP/RNBQKBNR b KQkq e3",
    "isCapture": false,
    "isCheck": false,
    "isMate": false,
    "isStalemate": false,
    "isFivefoldRepetition": false,
    "mode": "fen",
    "variant": "classical"
  }
}
```

A popular response to 1.e4 is 1...e5 which in LAN format is e7e5.

```js
ws.send('/play_lan b e7e5');
```

Once again the `/play_lan` command makes a chess move, this time retrieving the following JSON response.

```text
{
  "/play_lan": {
    "turn": "w",
    "pgn": "e5",
    "castlingAbility": "KQkq",
    "movetext": "1.e4 e5",
    "fen": "rnbqkbnr/pppp1ppp/8/4p3/4P3/8/PPPP1PPP/RNBQKBNR w KQkq e6",
    "isCapture": false,
    "isCheck": false,
    "isMate": false,
    "isStalemate": false,
    "isFivefoldRepetition": false,
    "mode": "fen",
    "variant": "classical"
  }
}
```

Let's recap.

Described below is the series of steps required to start a classical chess game with 1.e4 e5. Remember, computers and graphic user interfaces (GUIs) usually prefer the Long Algebraic Notation (LAN) format instead: e2e4 and e7e5.

```js
const ws = new WebSocket('wss://chesslablab.net:8443');
ws.send('/start classical fen');
ws.send('/play_lan w e2e4');
ws.send('/play_lan b e7e5');
```

Now let's have a look at the WebSocket commands available! The list of commands could have been sorted in alphabetical order but it is more convenient to begin with the `/start` command and continue in a way that's easier to understand.
