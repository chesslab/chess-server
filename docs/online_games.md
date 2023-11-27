# /online_games

Lists the number of online games in play, with information about those games. You can also use the command to join online games.

| Name | Description | Required |
| ---- | ----------- | -------- |
| `join` | Join an online game | Yes |

## Usage

### Example

```js
ws.send('/online_games');
```

```text
Game ID | Player 1 | Player 2 | Move Number | Position
------- | -------- | -------- | -------- | --------
1       | Steve    | Mark     | 1        | e4 e5
2       | Max      | Charles  | 2        | Nf3 Nc6
```
