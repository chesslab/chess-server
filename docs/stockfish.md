# /stockfish

Returns Stockfish's response to the current position.

## `params`

### `options`

Stockfish options.

- `Skill Level` is the skill level.

### `params`

Stockfish params.

- `depth` is the number of half moves the engine looks ahead.

---

## Usage

### Example

Start a classical game, play `e2e4` and use Stockfish to respond with a move.

```js
ws.send('/start "{\\"variant\\":\\"classical\\",\\"mode\\":\\"stockfish\\",\\"settings\\":{\\"color\\":\\"w\\"}}"');
ws.send('/play_lan "{\\"color\\":\\"w\\",\\"lan\\":\\"e2e4\\"}"');
ws.send('/stockfish "{\\"options\\":{\\"Skill Level\\":\\"20\\"},\\"params\\":{\\"depth\\":12}}"');
```

```text
{
  "/stockfish": {
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
    "isFiftyMoveDraw": false,
    "isDeadPositionDraw": false,
    "doesDraw": false,
    "doesWin": false,
    "mode": "stockfish",
    "variant": "classical"
  }
}
```
