# /heuristics

Takes a balanced heuristic picture of the given PGN movetext. A chess game can be plotted in terms of balance. +1 is the best possible evaluation for White and -1 the best possible evaluation for Black. Both forces being set to 0 means they're balanced.

## Parameters

| Name | Description | Required |
| ---- | ----------- | -------- |
| movetext | A portable game notation (PGN) movetext. | Yes |

### Example

```js
ws.send('/start classical analysis');
ws.send('/heuristics "1.d4 Nf6 2.c4 e6 3.Nf3 b6 4.Nc3"');
```

```text
{
  "/heuristics": {
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
      [ 0, 0.38, -0.37, 0.5, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0 ],
      ...
      [ 0, 0.43, 0, 0.25, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0 ]
    ]
  }
}
```

The returned data can then be plotted on a chart as shown in the example below.

![Figure 1](https://raw.githubusercontent.com/chesslablab/chess-server/master/docs/heuristics_01.png)
