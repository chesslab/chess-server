# /leave

Leaves a game.

## `color`

The color as per these options.

- `w` for the white pieces.
- `b` for the black pieces.

---

### Usage

#### Example

```js
ws.send('/leave "{\\"color\\":\\"b\\"}"');
```

```text
{
  "/leave": {
    "turn": "w",
    "movetext": "1.e4 e5 2.d4 d5",
    "fen": "rnbqkbnr/ppp2ppp/8/3pp3/3PP3/8/PPP2PPP/RNBQKBNR w KQkq d6",
    "end": {
      "result": "1-0",
      "msg": "White wins"
    },
    "color": "b"
  }
}
```
