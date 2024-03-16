# /start

Starts a new game.

| Name | Description | Required |
| ---- | ----------- | -------- |
| `variant` | `960`<br/>`capablanca`<br/>`capablanca-fischer`<br/>`classical` | Yes |
| `mode` | `fen`<br/>`san`<br/>`play`<br/>`stockfish` | Yes |
| `add` | `fen`<br/>`movetext`<br/>`startPos`<br/>`settings`<br/>`color` | Depends on the mode selected. |

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
ws.send('/start classical stockfish b');
```

```text
{
  "/start": {
    "variant": "classical",
    "mode": "stockfish",
    "color": "b"
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
    "jwt": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJhc3luYy5jaGVzc2xhYmxhYi5vcmciLCJpYXQiOjE3MTA1MjM2MzUsImV4cCI6MTcxMDUyNzIzNSwidmFyaWFudCI6ImNsYXNzaWNhbCIsInN1Ym1vZGUiOiJvbmxpbmUiLCJjb2xvciI6InciLCJtaW4iOiI1IiwiaW5jcmVtZW50IjoiMyIsImZlbiI6InJuYnFrYm5yL3BwcHBwcHBwLzgvOC84LzgvUFBQUFBQUFAvUk5CUUtCTlIgdyBLUWtxIC0ifQ.gGEgw7zZmJk3ftnS9Yu-RcvhFBzc9eKbvF_45hrEt4E",
    "hash": "db4a87907dfaaede54c56b3edddbe7e3a8c01803f8906be7bb7a2e180353ce58"
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
    "jwt": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJhc3luYy5jaGVzc2xhYmxhYi5vcmciLCJpYXQiOjE3MTA1MjM5MzUsImV4cCI6MTcxMDUyNzUzNSwidmFyaWFudCI6ImNsYXNzaWNhbCIsInN1Ym1vZGUiOiJmcmllbmQiLCJjb2xvciI6ImIiLCJtaW4iOiI1IiwiaW5jcmVtZW50IjoiMyIsImZlbiI6InJuYnFrYm5yL3BwcHBwcHBwLzgvOC84LzgvUFBQUFBQUFAvUk5CUUtCTlIgdyBLUWtxIC0ifQ.Tl_loA70IUidZ9XG9Pw9osC9MZIHbxjg_eDMkGS6des",
    "hash": "7eb1642c7d5faf2dee2e5dd5aef871c55fe29b5791c8e67746664e8f2517727c"
  }
}
```
