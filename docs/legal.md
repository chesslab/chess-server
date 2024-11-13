# /legal

Returns the legal moves of a piece.

## `square`

The location of the piece on the board.

---

### Usage

#### Example

Start a classical game to find out the legal moves of the piece on e2.

```js
ws.send('/start "{\\"variant\\":\\"classical\\",\\"mode\\":\\"analysis\\"}"');
ws.send('/legal "{\\"square\\":\\"e2\\"}"');
```

```text
{
  "/legal": [
    "e3",
    "e4"
  ]
}
```
