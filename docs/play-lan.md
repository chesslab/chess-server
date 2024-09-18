# /play_lan

Plays a move in long algebraic notation.

## `color`

The color as per these options.

- `w` for the white pieces.
- `b` for the black pieces.

## `lan`

The chess move in LAN format.

---

## Usage

### Example

Starts a classical game to play 1.e4.

```js
ws.send('/start "{\\"variant\\":\\"classical\\",\\"mode\\":\\"analysis\\"}"');
ws.send('/play_lan "{\\"color\\":\\"w\\",\\"lan\\":\\"e2e4\\"}"');
```

```text
{
  "/play_lan": {
    "turn": "b",
    "movetext": "1.e4",
    "fen": "rnbqkbnr/pppppppp/8/8/4P3/8/PPPP1PPP/RNBQKBNR b KQkq e3",
    "variant": "classical",
    "isValid": true
  }
}
```
