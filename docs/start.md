# /start

Starts a new game.

| Name | Description | Required |
| ---- | ----------- | -------- |
| `variant` | `960`<br/>`capablanca`<br/>`capablanca-fischer`<br/>`classical` | Yes |
| `mode` | `fen`<br/>`san`<br/>`play`<br/>`stockfish` | Yes. `fen` stands for Forsyth-Edwards Notation and is the standard way for starting games from specific chess positions. The `san` mode stands for Standard Algebraic Notation and is often used to load chess games from the starting position. The `play` mode allows to play chess online with other players while the `stockfish` mode is used to play chess against the computer. |
| `add` | `fen`<br/>`movetext`<br/>`startPos`<br/>`settings`<br/>`color` | Additional parameters may be required depending on the mode selected as shown in the examples below. |

## Usage

### Start a classical game

```js
ws.send('/start classical fen');
```

```text
{
  "/start": {
    "variant": "classical",
    "mode": "fen",
    "fen": "rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq -"
  }
}
```

### Start a classical game from a FEN position

| Name | Description | Required |
| ---- | ----------- | -------- |
| `add` | `fen` | Yes |

```js
ws.send('/start classical fen "{\\"fen\\":\\"r1bqkbnr/pppppppp/2n5/8/3PP3/8/PPP2PPP/RNBQKBNR b KQkq d3\\"}"');
```

```text
{
  "/start": {
    "variant": "classical",
    "mode": "fen",
    "fen": "r1bqkbnr/pppppppp/2n5/8/3PP3/8/PPP2PPP/RNBQKBNR b KQkq -"
  }
}
```

### Start a classical game from a SAN movetext

| Name | Description | Required |
| ---- | ----------- | -------- |
| `add` | `movetext` | Yes |

```js
ws.send('/start classical san "{\\"movetext\\":\\"1.e4 Nc6 2.d4\\"}"');
```

```text
{
  "/start": {
    "variant": "classical",
    "mode": "san",
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
| `add` | `movetext`<br/>`startPos` | Yes |

```js
ws.send('/start 960 san "{\\"movetext\\":\\"1.e4 Nc6 2.d4\\",\\"startPos\\":\\"BNRKQBRN\\"}"');
```

```text
{
  "/start": {
    "variant": "960",
    "mode": "san",
    "turn": "b",
    "movetext": "1.e4 Nc6 2.d4",
    "fen": [
      "bnrkqbrn/pppppppp/8/8/8/8/PPPPPPPP/BNRKQBRN w KQkq -",
      "bnrkqbrn/pppppppp/8/8/4P3/8/PPPP1PPP/BNRKQBRN b KQkq e3",
      "b1rkqbrn/pppppppp/2n5/8/4P3/8/PPPP1PPP/BNRKQBRN w KQkq -",
      "b1rkqbrn/pppppppp/2n5/8/3PP3/8/PPP2PPP/BNRKQBRN b KQkq d3"
    ],
    "startPos": "BNRKQBRN"
  }
}
```

### Start a classical game in Stockfish mode

| Name | Description | Required |
| ---- | ----------- | -------- |
| `add` | `color` | Yes |

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
    "jwt": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJhc3luYy5jaGVzc2xhYmxhYi5vcmciLCJpYXQiOjE3MTYzOTE1MTMsImV4cCI6MTcxNjM5NTExMywidmFyaWFudCI6ImNsYXNzaWNhbCIsInN1Ym1vZGUiOiJvbmxpbmUiLCJjb2xvciI6ImIiLCJtaW4iOjUsImluY3JlbWVudCI6MywiZmVuIjoicm5icWtibnIvcHBwcHBwcHAvOC84LzgvOC9QUFBQUFBQUC9STkJRS0JOUiB3IEtRa3EgLSJ9.BBJROgd2AP9_65QM3UIuxHk9HJ7ySwb_Y7HlVFFxnQE",
    "hash": "2a2c768c"
  }
}
```

### Create an invite code to play a classical game

| Name | Description | Required |
| ---- | ----------- | -------- |
| `add` | `settings` | Yes |

```js
ws.send('/start classical play "{\\"min\\":5,\\"increment\\":3,\\"color\\":\\"w\\",\\"submode\\":\\"friend\\"}"');
```

```text
{
  "/start": {
    "variant": "classical",
    "mode": "play",
    "fen": "rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq -",
    "jwt": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJhc3luYy5jaGVzc2xhYmxhYi5vcmciLCJpYXQiOjE3MTYzOTE1NTAsImV4cCI6MTcxNjM5NTE1MCwidmFyaWFudCI6ImNsYXNzaWNhbCIsInN1Ym1vZGUiOiJmcmllbmQiLCJjb2xvciI6InciLCJtaW4iOjUsImluY3JlbWVudCI6MywiZmVuIjoicm5icWtibnIvcHBwcHBwcHAvOC84LzgvOC9QUFBQUFBQUC9STkJRS0JOUiB3IEtRa3EgLSJ9.t40rbfZJb8JF8QpVMx9v96yQXzw54gLALMnKRS6qFdE",
    "hash": "c13d765b"
  }
}
```
