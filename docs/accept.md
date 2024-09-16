# /accept

Accepts an invitation to play online with an opponent.

## `params`

### `hash`

The unique hash of the game.

### `username`

The username accepting the invitation.

---

## Usage

### Example

```js
ws.send('/accept "{\\"hash\\":\\"5a3c9a56\\",\\"username\\":null,\\"elo\\":null}"');
```

```text
{
  "/accept": {
    "jwt": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJhc3luYy5jaGVzc2xhYmxhYi5vcmciLCJpYXQiOjE3MjY1MDE3NjEsImV4cCI6MTcyNjUwNTM2MSwidmFyaWFudCI6ImNsYXNzaWNhbCIsInVzZXJuYW1lIjp7InciOiJhbm9ueW1vdXMiLCJiIjoiYW5vbnltb3VzIn0sImVsbyI6eyJ3IjpudWxsLCJiIjpudWxsfSwic3VibW9kZSI6Im9ubGluZSIsImNvbG9yIjoidyIsIm1pbiI6IjUiLCJpbmNyZW1lbnQiOiIzIiwiZmVuIjoicm5icWtibnIvcHBwcHBwcHAvOC84LzgvOC9QUFBQUFBQUC9STkJRS0JOUiB3IEtRa3EgLSJ9.ziTYsSDK-aE9E51kq3PZSnZcZA4P2V33sZ8p5n0AFi4",
    "hash": "5a3c9a56",
    "timer": {
      "w": 300,
      "b": 300
    },
    "startedAt": 1726501844
  }
}
```

Decoded JWT:

```text
{
  "iss": "async.chesslablab.org",
  "iat": 1726501761,
  "exp": 1726505361,
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
  "color": "w",
  "min": "5",
  "increment": "3",
  "fen": "rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq -"
}
```
