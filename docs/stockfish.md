# /stockfish

Play a game with the Stockfish engine.

| Name | Description | Required |
| ---- | ----------- | -------- |
| `-t minutes` | Play a game against Stockfish with a time limit in minutes | No |
| `-b` | Play a game of chess against Stockfish with the black pieces | No |
| `-w` | Play a game of chess against Stockfish with the white pieces | No |

## Usage

### Example

```js
ws.send('/stockfish');
```

```text
1. e4 e5 2. Nf3 Nc6 3. Bb5 Nf6 4. Nxe5
```

This output shows what moves Stockfish has played throughout the game. The output varies depending on the game played.
