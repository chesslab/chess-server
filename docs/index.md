# Chess Server for Web Apps

The ChesslaBlab [Chess Server](https://github.com/chesslablab/chess-server) provides additional functionality to play chess online. It is based on WebSockets and can be hosted on a custom domain.

This is how to open a WebSocket connection in JavaScript.

```js
const ws = new WebSocket('wss://pchess.net:8443');
```

That's it!

Now you're set up to start playing chess.

```js
ws.send('/start classical analysis');
```

The `/start` command above starts a new classical chess game and retrieves a JSON response from the server.

```text
{
  "\/start": {
    "variant":"classical",
    "mode":"analysis",
    "fen":"rnbqkbnr\/pppppppp\/8\/8\/8\/8\/PPPPPPPP\/RNBQKBNR w KQkq -"
  }
}
```

On successful server response a FEN string representing the starting position is returned as well as the variant and the mode required.

This is the classical starting position in FEN format.

```text
rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq -
```

As you can see in the server's response, forward slashes are escaped with a backslash. From now on this will be assumed, and forward slashes won't be escaped for the sake of simplicity and for documentation purposes.

Now you're ready to make your first move.

What about 1.e4?

This is the so-called King's Pawn Game, one of the most popular chess openings, in Portable Game Notation (PGN) format. Humans can understand chess games in PGN easily but this format is not that great for computers as well as for graphic user interfaces (GUI) which may prefer the Long Algebraic Notation (LAN) format instead.

Let's play 1.e4 in LAN format.

```js
ws.send('/play_lan w e2e4');
```

The `/play_lan` command makes the chess move retrieving the following JSON response.

```text
{
  "/play_lan": {
    "turn": "w",
    "isLegal": true,
    "isCheck": false,
    "isMate": false,
    "movetext": "1.e4",
    "fen": "rnbqkbnr/pppppppp/8/8/4P3/8/PPPP1PPP/RNBQKBNR b KQkq e3",
    "pgn": "e4"
  }
}
```

A popular response to 1.e4 is 1...e5 which in LAN format is e7e5.

```js
ws.send('/play_lan b e7e5');
```

Once again the `/play_lan` command makes this chess move retrieving the following JSON response.

```text
{
  "/play_lan": {
    "turn": "b",
    "isLegal": true,
    "isCheck": false,
    "isMate": false,
    "movetext": "1.e4 e5",
    "fen": "rnbqkbnr/pppp1ppp/8/4p3/4P3/8/PPPP1PPP/RNBQKBNR w KQkq e6",
    "pgn": "e5"
  }
}
```

Let's recap.

Described below is the series of steps required to start a classical chess game with 1.e4 e5. Remember, computers and graphic user interfaces (GUIs) usually prefer the Long Algebraic Notation (LAN) format instead: e2e4 and e7e5.

```js
const ws = new WebSocket('wss://pchess.net:8443');
ws.send('/start classical analysis');
ws.send('/play_lan w e2e4');
ws.send('/play_lan b e7e5');
```

The `/start` command accepts two mandatory params: A chess variant and a game mode. These two play an important role in shaping the way a chess game is started so here's a description of both.

## Variant

| Name | Description |
| ---- | ----------- |
| 960 | Chess960, also known as Fischer Random chess. |
| capablanca | Capablanca chess played on a 10×8 board. |
| classical | Classical chess. |

## Mode

| Name | Description |
| ---- | ----------- |
| analysis | Start a game from the start position for further analysis. |
| fen | Start a game from a FEN position for further analysis. |
| pgn | Start a game from a PGN movetext for further analysis. |
| play | Start a game to play online with an opponent. |
| stockfish | Start a game to play with the Stockfish chess engine. |

Now let's have a look at the WebSocket commands available!

The list of commands could have been sorted in alphabetical order but it is more convenient to begin with the `/start` command and continue in a way that's easier to understand.
