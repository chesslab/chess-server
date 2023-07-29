# /start

Starts a new chess game.

## Parameters

| Name | Description | Required |
| ---- | ----------- | -------- |
| variant | Accepts: 960, capablanca, classical. | Yes |
| mode | Accepts: analysis, fen, pgn, play, stockfish. | Yes |
| add | Additional, specific params. color (stockfish mode), fen (fen mode), movetext (pgn mode), settings (play mode), startPos (pgn mode). | Maybe, depends on the mode selected. |

### Example

Starts a classical game for further analysis.

```js
ws.send('/start classical analysis');
```

```text
{
  "/start": {
    "variant": "classical",
    "mode": "analysis",
    "fen": "rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq -"
  }
}
```

### Example

Starts a classical game in FEN mode for further analysis.

```js
ws.send('/start classical fen "r1bqkbnr/pppppppp/2n5/8/3PP3/8/PPP2PPP/RNBQKBNR b KQkq d3"');
```

```text
{
  "/start": {
    "variant": "classical",
    "mode": "fen",
    "fen": "r1bqkbnr/pppppppp/2n5/8/3PP3/8/PPP2PPP/RNBQKBNR b KQkq d3"
  }
}
```

### Example

Starts a classical game in PGN mode for further analysis.

```js
ws.send('/start classical pgn "1.e4 Nc6 2.d4"');
```

```text
{
  "/start": {
    "variant": "classical",
    "mode": "pgn",
    "turn": "b",
    "movetext": "1.e4 Nc6 2.d4",
    "fen": "r1bqkbnr/pppppppp/2n5/8/3PP3/8/PPP2PPP/RNBQKBNR b KQkq d3",
    "history": [
      [
        [
          " r ",
          " n ",
          " b ",
          " q ",
          " k ",
          " b ",
          " n ",
          " r "
        ],
        ...
        [
          " R ",
          " N ",
          " B ",
          " Q ",
          " K ",
          " B ",
          " N ",
          " R "
        ]
      ]
    ]
  }
}
```

### Example

Starts a Chess960 game in PGN mode for further analysis.

```js
ws.send('/start 960 pgn "1.e4 Nc6 2.d4" BNRKQBRN');
```

```text
{
  "/start": {
    "variant": "960",
    "mode": "pgn",
    "turn": "b",
    "movetext": "1.e4 Nc6 2.d4",
    "fen": "b1rkqbrn/pppppppp/2n5/8/3PP3/8/PPP2PPP/BNRKQBRN b KQkq d3",
    "history": [
      [
        [
          " r ",
          " n ",
          " b ",
          " q ",
          " k ",
          " b ",
          " n ",
          " r "
        ],
        ...
        [
          " B ",
          " N ",
          " R ",
          " K ",
          " Q ",
          " B ",
          " R ",
          " N "
        ]
      ]
    ]
  }
}
```

### Example

Creates an invite code (a hash) to play a classical game with a friend.

```js
ws.send('/start classical play {"min":5,"increment":3,"color":"b","submode":"friend"}');
```

```text
{
  "/start": {
    "variant": "classical",
    "mode": "play",
    "fen": "rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq -",
    "jwt": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJwY2hlc3MubmV0IiwiaWF0IjoxNjc2NzYwNTgxLCJleHAiOjE2NzY3NjQxODEsInZhcmlhbnQiOiJjbGFzc2ljYWwiLCJzdWJtb2RlIjoiZnJpZW5kIiwiY29sb3IiOiJiIiwibWluIjo1LCJpbmNyZW1lbnQiOjMsImZlbiI6InJuYnFrYm5yXC9wcHBwcHBwcFwvOFwvOFwvOFwvOFwvUFBQUFBQUFBcL1JOQlFLQk5SIHcgS1FrcSAtIn0.jbVZGSaD9Q-QSrRkIdl-XXWMCuSV_4nrfJl28FObC24",
    "hash": "9eebcdf09342ef257407f341518b5d81"
  }
}
```

### Example

Starts a classical game in Stockfish mode.

```js
ws.send('/start classical stockfish b');
```

```text
{
  "/start": {
    "variant": "classical",
    "mode": "stockfish",
    "color": "b"
  }
}
```
