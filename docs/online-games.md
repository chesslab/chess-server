# /online_games

Returns the online games waiting to be accepted.

---

## Usage

### Example

```js
ws.send('/online_games');
```

```text
{
  "/online_games": [
    {
      "iss": "async.chesslablab.org",
      "iat": 1726500035,
      "exp": 1726503635,
      "variant": "classical",
      "username": {
        "w": "anonymous",
        "b": "anonymous"
      },
      "elo": {
        "w": null,
        "b": null
      },
      "submode": "online",
      "color": "b",
      "min": "13",
      "increment": "6",
      "fen": "rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq -",
      "hash": "c9d99e9d",
      "jwt": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJhc3luYy5jaGVzc2xhYmxhYi5vcmciLCJpYXQiOjE3MjY1MDAwMzUsImV4cCI6MTcyNjUwMzYzNSwidmFyaWFudCI6ImNsYXNzaWNhbCIsInVzZXJuYW1lIjp7InciOiJhbm9ueW1vdXMiLCJiIjoiYW5vbnltb3VzIn0sImVsbyI6eyJ3IjpudWxsLCJiIjpudWxsfSwic3VibW9kZSI6Im9ubGluZSIsImNvbG9yIjoiYiIsIm1pbiI6IjEzIiwiaW5jcmVtZW50IjoiNiIsImZlbiI6InJuYnFrYm5yL3BwcHBwcHBwLzgvOC84LzgvUFBQUFBQUFAvUk5CUUtCTlIgdyBLUWtxIC0ifQ.I-ikZ1ZYPrmbi6XKpm4Pz8rwtMaZu6jui3laTgWuHpk"
    },
    {
      "iss": "async.chesslablab.org",
      "iat": 1726500050,
      "exp": 1726503650,
      "variant": "960",
      "username": {
        "w": "anonymous",
        "b": "anonymous"
      },
      "elo": {
        "w": null,
        "b": null
      },
      "submode": "online",
      "color": "w",
      "min": "5",
      "increment": "3",
      "fen": "bbnrkrqn/pppppppp/8/8/8/8/PPPPPPPP/BBNRKRQN w KQkq -",
      "startPos": "BBNRKRQN",
      "hash": "21b2a385",
      "jwt": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJhc3luYy5jaGVzc2xhYmxhYi5vcmciLCJpYXQiOjE3MjY1MDAwNTAsImV4cCI6MTcyNjUwMzY1MCwidmFyaWFudCI6Ijk2MCIsInVzZXJuYW1lIjp7InciOiJhbm9ueW1vdXMiLCJiIjoiYW5vbnltb3VzIn0sImVsbyI6eyJ3IjpudWxsLCJiIjpudWxsfSwic3VibW9kZSI6Im9ubGluZSIsImNvbG9yIjoidyIsIm1pbiI6IjUiLCJpbmNyZW1lbnQiOiIzIiwiZmVuIjoiYmJucmtycW4vcHBwcHBwcHAvOC84LzgvOC9QUFBQUFBQUC9CQk5SS1JRTiB3IEtRa3EgLSIsInN0YXJ0UG9zIjoiQkJOUktSUU4ifQ.JoQ6VyOjYoCzMLGQ8rs3w7sg6fLPxWIP0lmAvvIzpmQ"
    }
  ]
}
```
