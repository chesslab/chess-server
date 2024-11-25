# /start

Starts a new game.

## `variant`

The chess variant as per these options.

- `classical` chess, also known as standard or slow chess.
- `960` is the same as classical chess except that the starting position of the pieces is randomized.
- `dunsany` is an asymmetric variant in which Black has the standard chess army and White has 32 pawns.
- `losing` chess, the objective of each player is to lose all of their pieces or be stalemated.
- `racing-kings` consists of being the first player to move their king to the eighth row.
- `capablanca` incorporates two new pieces, the archbishop and the chancellor, and is played on a 10×8 board.
- `capablanca-fischer` is a mix of Capablanca chess and Chess960.

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

### Usage

### Start a classical game

```js
ws.send('/start "{\\"variant\\":\\"classical\\",\\"mode\\":\\"analysis\\"}"');
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
ws.send('/start "{\\"variant\\":\\"classical\\",\\"mode\\":\\"analysis\\",\\"settings\\":{\\"fen\\":\\"r1bqkbnr/pppppppp/2n5/8/3PP3/8/PPP2PPP/RNBQKBNR b KQkq d3\\",\\"movetext\\":\\"\\"}}"');
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
ws.send('/start "{\\"variant\\":\\"classical\\",\\"mode\\":\\"analysis\\",\\"settings\\":{\\"fen\\":\\"rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq -\\",\\"movetext\\":\\"1.e4 Nc6 2.d4\\"}}"');
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
ws.send('/start "{\"variant\":\"960\",\"mode\":\"analysis\",\"settings\":{\"fen\":\"bnrkqbrn/pppppppp/8/8/8/8/PPPPPPPP/BNRKQBRN w KQkq -\",\"movetext\":\"1.e4 Nc6 2.d4\",\"startPos\":\"RBQKBNRN\"}}"');
```

```text
{
  "/start": {
    "variant": "960",
    "mode": "analysis",
    "turn": "b",
    "movetext": "1.e4 Nc6 2.d4",
    "fen": [
      "bnrkqbrn/pppppppp/8/8/8/8/PPPPPPPP/BNRKQBRN w KQkq -",
      "bnrkqbrn/pppppppp/8/8/4P3/8/PPPP1PPP/BNRKQBRN b KQkq e3",
      "b1rkqbrn/pppppppp/2n5/8/4P3/8/PPPP1PPP/BNRKQBRN w KQkq -",
      "b1rkqbrn/pppppppp/2n5/8/3PP3/8/PPP2PPP/BNRKQBRN b KQkq d3"
    ],
    "startPos": "RBQKBNRN"
  }
}
```

### Start a classical game in Stockfish mode

| Name | Description | Required |
| ---- | ----------- | -------- |
| `settings` | `color` | Yes |

```js
ws.send('/start "{\\"variant\\":\\"classical\\",\\"mode\\":\\"stockfish\\",\\"settings\\":{\\"color\\":\\"b\\"}}"');
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

| Name | Description | Required |
| ---- | ----------- | -------- |
| `settings` | `min`<br/>`increment`<br/>`color`<br/>`submode` | Yes |

```js
ws.send('/start "{\\"variant\\":\\"classical\\",\\"mode\\":\\"play\\",\\"settings\\":{\\"min\\":\\"5\\",\\"increment\\":\\"3\\",\\"color\\":\\"b\\",\\"submode\\":\\"online\\",\\"username\\":null}}"');
```

```text
{
  "/start": {
    "uid": "f3caa3d1",
    "jwt": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJhc3luYy5jaGVzc2xhYmxhYi5vcmciLCJpYXQiOjE3MjY1OTkxMDMsImV4cCI6MTcyNjYwMjcwMywidmFyaWFudCI6ImNsYXNzaWNhbCIsInVzZXJuYW1lIjp7InciOiJhbm9ueW1vdXMiLCJiIjoiYW5vbnltb3VzIn0sImVsbyI6eyJ3IjpudWxsLCJiIjpudWxsfSwic3VibW9kZSI6Im9ubGluZSIsImNvbG9yIjoiYiIsIm1pbiI6IjUiLCJpbmNyZW1lbnQiOiIzIiwiZmVuIjoicm5icWtibnIvcHBwcHBwcHAvOC84LzgvOC9QUFBQUFBQUC9STkJRS0JOUiB3IEtRa3EgLSIsInVpZCI6Ijc2M2Q1OGEyIn0.dMLEBGBJPsdII3KReOXYXoOZFmeQVJyku0q_mG2dZnk",
    "variant": "classical",
    "mode": "play",
    "fen": "rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq -"
  }
}
```

### Create an invite code to play a classical game

| Name | Description | Required |
| ---- | ----------- | -------- |
| `settings` | `min`<br/>`increment`<br/>`color`<br/>`submode` | Yes |

```js
ws.send('/start "{\\"variant\\":\\"classical\\",\\"mode\\":\\"play\\",\\"settings\\":{\\"min\\":\\"5\\",\\"increment\\":\\"3\\",\\"color\\":\\"w\\",\\"submode\\":\\"friend\\",\\"username\\":null}}"');
```

```text
{
  "/start": {
    "uid": "fbb4a3f3",
    "jwt": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJhc3luYy5jaGVzc2xhYmxhYi5vcmciLCJpYXQiOjE3MjY1OTkxOTYsImV4cCI6MTcyNjYwMjc5NiwidmFyaWFudCI6ImNsYXNzaWNhbCIsInVzZXJuYW1lIjp7InciOiJhbm9ueW1vdXMiLCJiIjoiYW5vbnltb3VzIn0sImVsbyI6eyJ3IjpudWxsLCJiIjpudWxsfSwic3VibW9kZSI6ImZyaWVuZCIsImNvbG9yIjoidyIsIm1pbiI6IjUiLCJpbmNyZW1lbnQiOiIzIiwiZmVuIjoicm5icWtibnIvcHBwcHBwcHAvOC84LzgvOC9QUFBQUFBQUC9STkJRS0JOUiB3IEtRa3EgLSIsInVpZCI6IjhkZjk1OGMyIn0.hWREwWcV-oq1EE_EhNrK9xFl1z8NTPxtG6FGeDkGLEE",
    "variant": "classical",
    "mode": "play",
    "fen": "rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq -"
  }
}
```
