# /legal

Returns the legal moves of a piece.

## `position`

The location of the piece on the board.

---

## Usage

### Example

Start a classical game to find out the legal moves of the piece on e2.

```js
ws.send('/start classical analysis');
ws.send('/legal e2');
```

```text
{
  "/legal": [
    "e3",
    "e4"
  ]
}
```
