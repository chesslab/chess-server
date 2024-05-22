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
      "iss": "async.chesslablab.org",
      "iat": 1716395884,
      "exp": 1716399484,
      "variant": "classical",
      "submode": "online",
      "color": "b",
      "min": "17",
      "increment": "8",
      "fen": "rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq -",
      "hash": "ec82790d"
    },
    {
      "iss": "async.chesslablab.org",
      "iat": 1716395890,
      "exp": 1716399490,
      "variant": "960",
      "submode": "online",
      "color": "w",
      "min": "5",
      "increment": "3",
      "fen": "brnnkbrq/pppppppp/8/8/8/8/PPPPPPPP/BRNNKBRQ w KQkq -",
      "startPos": "BRNNKBRQ",
      "hash": "c0597f26"
    }
  ]
}
```
