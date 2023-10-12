# /start

## Description

Starts a new game.

## Parameters

| Name | Description | Required |
| ---- | ----------- | -------- |
| `variant` | `960`<br/>`capablanca`<br/>`capablanca-fischer`<br/>`classical` | Yes |
| `mode` | `fen`<br/>`san`<br/>`play`<br/>`stockfish` | Yes |
| `add` | `fen`<br/>`movetext`<br/>`startPos`<br/>`settings`<br/>`color`<br/>`min`<br/>`increment`<br/>`submode` | Maybe. Depends on the mode selected. |

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

### Start a classical FEN

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

### Start a classical SAN movetext

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

### Start a Chess960 SAN movetext

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

### Create an invite code to play a classical game

```js
ws.send('/start classical play "{\\"min\\":5,\\"increment\\":3,\\"color\\":\\"w\\",\\"submode\\":\\"friend\\"}"');
```

```text
{
  "/start": {
    "variant": "classical",
    "mode": "play",
    "fen": "rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq -",
    "jwt": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJwY2hlc3MubmV0IiwiaWF0IjoxNjkzOTI5MDUzLCJleHAiOjE2OTM5MzI2NTMsInZhcmlhbnQiOiJjbGFzc2ljYWwiLCJzdWJtb2RlIjoiZnJpZW5kIiwiY29sb3IiOiJ3IiwibWluIjo1LCJpbmNyZW1lbnQiOjMsImZlbiI6InJuYnFrYm5yL3BwcHBwcHBwLzgvOC84LzgvUFBQUFBQUFAvUk5CUUtCTlIgdyBLUWtxIC0ifQ.1rdf1MMR3vsYMDYZ0LroMlTbYRFp3j1ctd1lshW0XV4",
    "hash": "0151f4d3af859a6aa14abd3e0ee57f2a"
  }
}
```
