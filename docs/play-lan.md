# /play_lan

Plays a chess move in long algebraic notation (LAN) format.

## Parameters

| Name | Description | Required |
| ---- | ----------- | -------- |
| color | The player's turn. | Yes |
| lan | The chess move in LAN format. | Yes |

### Example

Starts a classical game to play 1.e4.

```js
ws.send('/start classical analysis');
ws.send('/play_lan w e2e4');
```

```text
{
  "/play_lan": {
    "turn": "w",
    "isLegal": true,
    "isCheck": false,
    "isMate": false,
    "movetext": "1.e4",
    "fen": "rnbqkbnr/pppppppp/8/8/4P3/8/PPPP1PPP/RNBQKBNR b KQkq e3",
    "pgn": "e4"
  }
}
```
