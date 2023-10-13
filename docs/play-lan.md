# /play_lan

Plays a chess move in long algebraic notation.

| Name | Description | Required |
| ---- | ----------- | -------- |
| `color` | The player's turn. | Yes |
| `lan` | The chess move in LAN format. | Yes |

## Usage

### Example

Starts a classical game to play 1.e4.

```js
ws.send('/start classical fen');
ws.send('/play_lan w e2e4');
```

```text
{
  "/play_lan": {
    "turn": "b",
    "pgn": "e4",
    "castlingAbility": "KQkq",
    "movetext": "1.e4",
    "fen": "rnbqkbnr/pppppppp/8/8/4P3/8/PPPP1PPP/RNBQKBNR b KQkq e3",
    "isCheck": false,
    "isMate": false,
    "isStalemate": false,
    "isFivefoldRepetition": false,
    "mode": "fen",
    "variant": "classical"
  }
}
```
