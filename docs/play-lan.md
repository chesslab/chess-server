# /play_lan

Plays a chess move in long algebraic notation.

## `settings`

### `color`

The color as per these options.

- `w` for the white pieces.
- `b` for the black pieces.

### `lan`

The chess move in LAN format.

---

## Usage

### Example

Starts a classical game to play 1.e4.

```js
ws.send('/start classical analysis');
ws.send('/play_lan "{\\"color\\":\\"w\\",\\"lan\\":\\"e2e4\\"}"');
```

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
    "isFiftyMoveDraw": false,
    "isDeadPositionDraw": false,
    "doesDraw": false,
    "doesWin": false,
    "mode": "analysis",
    "variant": "classical",
    "isValid": true
  }
}
```
