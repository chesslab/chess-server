# /play

Plays a move in Portable Game Notation (PGN) format.

## `color`

The color as per these options.

- `w` for the white pieces.
- `b` for the black pieces.

## `pgn`

The chess move in PGN format.

---

### Usage

#### Example

Starts a classical game to play 1.e4.

```js
ws.send('/start "{\\"variant\\":\\"classical\\",\\"mode\\":\\"analysis\\"}"');
ws.send('/play "{\\"color\\":\\"w\\",\\"pgn\\":\\"e4\\"}"');
```

```text
{
  "/play": {
    "turn": "b",
    "movetext": "1.e4",
    "fen": "rnbqkbnr/pppppppp/8/8/4P3/8/PPPP1PPP/RNBQKBNR b KQkq e3",
    "variant": "classical",
    "isValid": true
  }
}
```
