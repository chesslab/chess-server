# /legal

Returns the legal FEN positions of a piece.

| Name | Description | Required |
| ---- | ----------- | -------- |
| `position` | The location of the piece on the board. | Yes |

## Usage

### Example

Start a classical game to find out the legal moves of the piece on e2.

```js
ws.send('/start classical fen');
ws.send('/legal e2');
```

```text
{
  "/legal": {
    "color": "w",
    "id": "P",
    "fen": {
      "e3": "rnbqkbnr/pppppppp/8/8/8/4P3/PPPP1PPP/RNBQKBNR b KQkq -",
      "e4": "rnbqkbnr/pppppppp/8/8/4P3/8/PPPP1PPP/RNBQKBNR b KQkq e3"
    }
  }
}
```
