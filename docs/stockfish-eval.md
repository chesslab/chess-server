# /stockfish_eval

Returns Stockfish's evaluation for the given position.

| Name | Description | Required |
| ---- | ----------- | -------- |
| `fen` | A FEN string. | Yes |

## Usage

### Example

Evaluate C65 — Ruy Lopez: Berlin Defense.

```js
ws.send('/stockfish_eval "r1bqkb1r/pppp1ppp/2n2n2/1B2p3/4P3/5N2/PPPP1PPP/RNBQK2R w KQkq - 4 4"');
```

```text
{
  "/stockfish_eval": {
    "nag": "$10",
    "meaning": "Equal position",
    "symbol": "="
  }
}
```
