# /undo

Undoes the last move.

---

### Usage

#### Example

Starts a classical game to play 1.e4 e5 2.f4 undoing the last move.

```js
ws.send('/start "{\\"variant\\":\\"classical\\",\\"mode\\":\\"analysis\\"}"');
ws.send('/play_lan "{\\"color\\":\\"w\\",\\"lan\\":\\"e2e4\\"}"');
ws.send('/play_lan "{\\"color\\":\\"b\\",\\"lan\\":\\"e7e5\\"}"');
ws.send('/play_lan "{\\"color\\":\\"w\\",\\"lan\\":\\"f2f4\\"}"');
ws.send('/undo');
```

```text
{
  "/undo": {
    "turn": "w",
    "movetext": "1.e4 e5",
    "fen": "rnbqkbnr/pppp1ppp/8/4p3/4P3/8/PPPP1PPP/RNBQKBNR w KQkq e6",
    "variant": "classical"
  }
}
```
