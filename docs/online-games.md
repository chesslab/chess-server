# /online_games

Returns the online games waiting to be accepted.

## Usage

### Example

```js
ws.send('/online_games');
```

```text
{
  "/online_games": [
    {
      "iss": "async.chesslablab.net",
      "iat": 1704983331,
      "exp": 1704986931,
      "variant": "classical",
      "submode": "online",
      "color": "b",
      "min": 5,
      "increment": 1,
      "fen": "rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq -",
      "hash": "84a4e20138e54e1869b9b79e7d3111f1"
    },
    {
      "iss": "async.chesslablab.net",
      "iat": 1704983347,
      "exp": 1704986947,
      "variant": "960",
      "submode": "online",
      "color": "w",
      "min": 10,
      "increment": 5,
      "fen": "bqrnnbkr/pppppppp/8/8/8/8/PPPPPPPP/BQRNNBKR w KQkq -",
      "startPos": "BQRNNBKR",
      "hash": "c6b79ac68055766c2a33364f69c72e4c"
    },
    {
      "iss": "async.chesslablab.net",
      "iat": 1704983367,
      "exp": 1704986967,
      "variant": "capablanca",
      "submode": "online",
      "color": "b",
      "min": 30,
      "increment": 10,
      "fen": "rnabqkbcnr/pppppppppp/10/10/10/10/PPPPPPPPPP/RNABQKBCNR w KQkq -",
      "hash": "076bafad0559def2db7866609680074c"
    }
  ]
}
```
