# /heuristics

Returns the heuristics of a chess position.

| Name | Description | Required |
| ---- | ----------- | -------- |
| `fen` | A FEN string. | Yes |
| `variant` | `960`<br/>`capablanca`<br/>`capablanca-fischer`<br/>`classical` | Yes |

## Usage

### Example

```js
ws.send('/start classical fen');
ws.send('/heuristics "rnbqkb1r/p1pp1ppp/1p2pn2/8/2PP4/2N2N2/PP2PPPP/R1BQKB1R b KQkq -" classical');
```

```text
{
  "/heuristics": {
    "names": [
      "Material",
      "Center",
      "Connectivity",
      "Space",
      "Pressure",
      "King safety",
      "Protection",
      "Threat",
      "Attack",
      "Doubled pawn",
      "Passed pawn",
      "Advanced pawn",
      "Far-advanced pawn",
      "Isolated pawn",
      "Backward pawn",
      "Defense",
      "Absolute skewer",
      "Absolute pin",
      "Relative pin",
      "Absolute fork",
      "Relative fork",
      "Outpost square",
      "Knight outpost",
      "Bishop outpost",
      "Bishop pair",
      "Bad bishop",
      "Diagonal opposition",
      "Direct opposition"
    ],
    "balance": [
      0,
      12.4,
      0,
      3,
      0,
      0,
      0,
      0,
      0,
      0,
      0,
      0,
      0,
      0,
      0,
      0,
      0,
      0,
      0,
      0,
      0,
      0,
      0,
      0,
      0,
      0,
      0,
      0
    ]
  }
}
```
