# /start

Starts a new game.

## `variant`

The chess variant as per these options.

- `classical` chess, also known as standard or slow chess.
- `960` is the same as classical chess except that the starting position of the pieces is randomized.
- `dunsany` is an asymmetric variant in which Black has the standard chess army and White has 32 pawns.
- `losing` chess, the objective of each player is to lose all of their pieces or be stalemated.
- `racing-kings` consists of being the first player to move their king to the eighth row.

## `mode`

The game mode as per these options.

- `analysis` is used to start games for further analysis.
- `play` allows to play chess online with other players.
- `stockfish` allows to play chess against the computer.

## `settings` (optional)

Additional optional parameters may be required depending on the mode selected as shown in the examples below.

- `color`
- `fen`
- `increment`
- `min`
- `movetext`
- `startPos`
- `submode`

---

## Usage

### Start a classical game

```js
ws.send('/start classical analysis');
```

```text
{
  "/start": {
    "variant": "classical",
    "mode": "analysis",
    "turn": "w",
    "movetext": "",
    "fen": [
      "rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq -"
    ]
  }
}
```

### Start a classical game from a FEN position

| Name | Description | Required |
| ---- | ----------- | -------- |
| `settings` | `fen` | Yes |

```js
ws.send('/start classical analysis "{\\"fen\\":\\"r1bqkbnr/pppppppp/2n5/8/3PP3/8/PPP2PPP/RNBQKBNR b KQkq d3\\"}"');
```

```text
{
  "/start": {
    "variant": "classical",
    "mode": "analysis",
    "turn": "b",
    "movetext": "",
    "fen": [
      "r1bqkbnr/pppppppp/2n5/8/3PP3/8/PPP2PPP/RNBQKBNR b KQkq -"
    ]
  }
}
```

### Start a classical game from a SAN movetext

| Name | Description | Required |
| ---- | ----------- | -------- |
| `settings` | `movetext` | Yes |

```js
ws.send('/start classical analysis "{\\"movetext\\":\\"1.e4 Nc6 2.d4\\"}"');
```

```text
{
  "/start": {
    "variant": "classical",
    "mode": "analysis",
    "turn": "b",
    "movetext": "1.e4 Nc6 2.d4",
    "fen": [
      "rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq -",
      "rnbqkbnr/pppppppp/8/8/4P3/8/PPPP1PPP/RNBQKBNR b KQkq e3",
      "r1bqkbnr/pppppppp/2n5/8/4P3/8/PPPP1PPP/RNBQKBNR w KQkq -",
      "r1bqkbnr/pppppppp/2n5/8/3PP3/8/PPP2PPP/RNBQKBNR b KQkq d3"
    ]
  }
}
```

### Start a Chess960 game from a SAN movetext

| Name | Description | Required |
| ---- | ----------- | -------- |
| `settings` | `movetext`<br/>`startPos` | Yes |

```js
ws.send('/start 960 analysis "{\\"movetext\\":\\"1.e4 Nc6 2.d4\\",\\"startPos\\":\\"BNRKQBRN\\"}"');
```

```text
{
  "/start": {
    "variant": "960",
    "mode": "analysis",
    "turn": "b",
    "movetext": "1.e4 Nc6 2.d4",
    "fen": [
      "rnbkqbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBKQBNR w KQkq -",
      "rnbkqbnr/pppppppp/8/8/4P3/8/PPPP1PPP/RNBKQBNR b KQkq e3",
      "r1bkqbnr/pppppppp/2n5/8/4P3/8/PPPP1PPP/RNBKQBNR w KQkq -",
      "r1bkqbnr/pppppppp/2n5/8/3PP3/8/PPP2PPP/RNBKQBNR b KQkq d3"
    ],
    "startPos": "RNBKQBNR"
  }
}
```

### Start a classical game in Stockfish mode

| Name | Description | Required |
| ---- | ----------- | -------- |
| `settings` | `color` | Yes |

```js
ws.send('/start classical stockfish {"color":"b"}');
```

```text
{
  "/start": {
    "variant": "classical",
    "mode": "stockfish",
    "color": "b",
    "fen": "rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq -"
  }
}
```

### Start a classical game to play online

```js
ws.send('/start classical play {"min":5,"increment":3,"color":"b","submode":"online"}');
```

```text
{
  "/start": {
    "variant": "classical",
    "mode": "play",
    "fen": "rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq -",
    "jwt": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJhc3luYy5jaGVzc2xhYmxhYi5vcmciLCJpYXQiOjE3MjE0MDIyNzksImV4cCI6MTcyMTQwNTg3OSwidmFyaWFudCI6ImNsYXNzaWNhbCIsInN1Ym1vZGUiOiJvbmxpbmUiLCJjb2xvciI6ImIiLCJtaW4iOjUsImluY3JlbWVudCI6MywiZmVuIjoicm5icWtibnIvcHBwcHBwcHAvOC84LzgvOC9QUFBQUFBQUC9STkJRS0JOUiB3IEtRa3EgLSJ9.ftJf7UkcL7EwrjsKQh29VgLHKVtKXggl5TZIruRdKoA",
    "hash": "70a777dd"
  }
}
```

### Create an invite code to play a classical game

| Name | Description | Required |
| ---- | ----------- | -------- |
| `settings` | `min`<br/>`increment`<br/>`color`<br/>`submode` | Yes |

```js
ws.send('/start classical play "{\\"min\\":5,\\"increment\\":3,\\"color\\":\\"w\\",\\"submode\\":\\"friend\\"}"');
```

```text
{
  "/start": {
    "variant": "classical",
    "mode": "play",
    "fen": "rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq -",
    "jwt": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJhc3luYy5jaGVzc2xhYmxhYi5vcmciLCJpYXQiOjE3MjE0MDI1MDYsImV4cCI6MTcyMTQwNjEwNiwidmFyaWFudCI6ImNsYXNzaWNhbCIsInN1Ym1vZGUiOiJmcmllbmQiLCJjb2xvciI6InciLCJtaW4iOjUsImluY3JlbWVudCI6MywiZmVuIjoicm5icWtibnIvcHBwcHBwcHAvOC84LzgvOC9QUFBQUFBQUC9STkJRS0JOUiB3IEtRa3EgLSJ9.myjbwTAy_z8CxnPwIQiJqzqYpGmj8bg52R89HB53NrQ",
    "hash": "3b9777a6"
  }
}
```
