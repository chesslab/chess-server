# /stockfish

Returns Stockfish's response to the current position.

## `Skill Level`

The skill level.

## `depth`

The number of half moves the engine looks ahead.

---

## Usage

### Example

Start a classical game, play `e2e4` and use Stockfish to respond with a move.

```js
ws.send('/start classical stockfish w');
ws.send('/play_lan w e2e4');
ws.send('/stockfish "{\\"Skill Level\\":20}" "{\\"depth\\":12}"');
```

```text
{
  "/stockfish": {
    "turn": "w",
    "pgn": "c5",
    "castlingAbility": "KQkq",
    "movetext": "1.e4 c5",
    "fen": "rnbqkbnr/pp1ppppp/8/2p5/4P3/8/PPPP1PPP/RNBQKBNR w KQkq c6",
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
