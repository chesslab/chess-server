# /tutor_fen

Explains a FEN position in terms of chess concepts.

## `fen`

A FEN string.

---

### Usage

#### Example

```js
ws.send('/tutor_fen "{\\"fen\\":\\"rnbqkb1r/1p2pppp/p2p1n2/8/3NP3/2N5/PPP2PPP/R1BQKB1R w KQkq -\\"}"');
```

```text
{"\/tutor_fen":"White is totally controlling the center. The black pieces are totally better connected. White has a moderate space advantage. White has a checkability advantage. Black's king on e8 can be checked. Overall, 3 heuristic evaluation features are favoring White while 1 is favoring Black."}
```
