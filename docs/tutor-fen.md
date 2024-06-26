# /tutor_fen

Explains a FEN position in terms of chess concepts.

## `variant`

The chess variant as per these options.

- `960`
- `classical`

## `fen`

A FEN string.

## Usage

### Example

```js
ws.send('/start classical fen');
ws.send('/tutor_fen "rnbqkb1r/p1pp1ppp/1p2pn2/8/2PP4/2N2N2/PP2PPPP/R1BQKB1R b KQkq -" classical');
```

```text
{
  "/tutor_fen": "White is totally controlling the center. White has a slight space advantage. Overall, 2 heuristic evaluation features are favoring White while 0 are favoring Black."
}
```
