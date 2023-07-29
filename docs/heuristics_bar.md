# /heuristics_bar

Similar to the `/heuristics` command with the difference that `/heuristics_bar` takes a picture of the current position only rather than a picture of the entire game.

## Parameters

| Name | Description | Required |
| ---- | ----------- | -------- |
| fen | A FEN string. | Yes |
| variant | Accepts: 960, capablanca, classical. | Yes |

### Example

```js
ws.send('/start classical analysis');
ws.send('/heuristics_bar "rnbqkb1r/p1pp1ppp/1p2pn2/8/2PP4/2N2N2/PP2PPPP/R1BQKB1R b KQkq -" classical');
```

```text
{
  "/heuristics_bar": {
    "dimensions": [
      "Material",
      "Center",
      "Connectivity",
      "Space",
      "Pressure",
      "King safety",
      "Tactics",
      "Attack",
      "Doubled pawn",
      "Passed pawn",
      "Isolated pawn",
      "Backward pawn",
      "Absolute pin",
      "Relative pin",
      "Absolute fork",
      "Relative fork",
      "Square outpost",
      "Knight outpost",
      "Bishop outpost",
      "Bishop pair",
      "Bad bishop",
      "Direct opposition"
    ],
    "balance": [
      0, 0.28, 0, 0.07, 0, 0, 0, 0, 0, 0, 0, 0.04, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0
    ]
  }
}
```

The returned data can then be plotted on a chart as shown in the example below.

![Figure 1](https://raw.githubusercontent.com/chesslablab/chess-server/master/docs/heuristics_bar_01.png)
