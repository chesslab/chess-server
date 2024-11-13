# /resign

Resigns a game.

## `color`

The color as per these options.

- `w` for the white pieces.
- `b` for the black pieces.

---

### Usage

#### Example

```js
ws.send('/resign "{\\"color\\":\\"b\\"}"');
```

```text
{
  "/resign": {
    "turn": "b",
    "movetext": "1.e4 e5 2.f4",
    "fen": "rnbqkbnr/pppp1ppp/8/4p3/4PP2/8/PPPP2PP/RNBQKBNR b KQkq f3",
    "end": {
      "result": "1-0",
      "msg": "White wins"
    },
    "color": "b"
  }
}
```
