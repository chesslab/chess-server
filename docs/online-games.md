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
      "iat": 1704983331,
      "exp": 1704986931,
      "variant": "classical",
      "submode": "online",
      "color": "b",
      "min": 5,
      "increment": 1,
      "fen": "rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq -",
      "hash": "d1a4f2f3688e3211e94fe643a679204806cd196303e44522e833815e4f728c65"
    },
    {
      "iss": "async.chesslablab.org",
      "iat": 1704983347,
      "exp": 1704986947,
      "variant": "960",
      "submode": "online",
      "color": "w",
      "min": 10,
      "increment": 5,
      "fen": "bqrnnbkr/pppppppp/8/8/8/8/PPPPPPPP/BQRNNBKR w KQkq -",
      "startPos": "BQRNNBKR",
      "hash": "e71a27a35d021acfe9ad819348ec2c574f1962aa14fddc58df379d0b3c225cc8"
    },
    {
      "iss": "async.chesslablab.org",
      "iat": 1704983367,
      "exp": 1704986967,
      "variant": "capablanca",
      "submode": "online",
      "color": "b",
      "min": 30,
      "increment": 10,
      "fen": "rnabqkbcnr/pppppppppp/10/10/10/10/PPPPPPPPPP/RNABQKBCNR w KQkq -",
      "hash": "2e2c447c902a34f55145769fe9454cf055a6b3e7658756ade2b2ac783d24c568"
    }
  ]
}
```
