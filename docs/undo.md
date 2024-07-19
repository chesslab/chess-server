# /undo

Undoes the last move.

---

## Usage

### Example

Starts a classical game to play 1.e4 e5 2.f4 undoing the last move.

```js
ws.send('/start classical analysis');
ws.send('/play_lan w e2e4');
ws.send('/play_lan b e7e5');
ws.send('/play_lan w f2f4');
ws.send('/undo');
```

```text
{
  "/undo": {
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
    "mode": "analysis",
    "variant": "classical"
  }
}
```
