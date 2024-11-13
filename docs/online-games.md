# /online_games

Returns the online games in pending status to be accepted.

---

### Usage

#### Example

```js
ws.send('/online_games');
```

```text
{
  "/online_games": [
    {
      "iss": "async.chesslablab.org",
      "iat": 1726598995,
      "exp": 1726602595,
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
      "fen": "rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq -",
      "uid": "3498a2d9",
      "jwt": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJhc3luYy5jaGVzc2xhYmxhYi5vcmciLCJpYXQiOjE3MjY1OTg5OTUsImV4cCI6MTcyNjYwMjU5NSwidmFyaWFudCI6ImNsYXNzaWNhbCIsInVzZXJuYW1lIjp7InciOiJhbm9ueW1vdXMiLCJiIjoiYW5vbnltb3VzIn0sImVsbyI6eyJ3IjpudWxsLCJiIjpudWxsfSwic3VibW9kZSI6Im9ubGluZSIsImNvbG9yIjoidyIsIm1pbiI6IjUiLCJpbmNyZW1lbnQiOiIzIiwiZmVuIjoicm5icWtibnIvcHBwcHBwcHAvOC84LzgvOC9QUFBQUFBQUC9STkJRS0JOUiB3IEtRa3EgLSIsInVpZCI6Ijk2YjM1OGQyIn0.jTDBWXRJ1rjhBxTT-cVER0Br9fq55wiL4f8UNWOkCDU"
    },
    {
      "iss": "async.chesslablab.org",
      "iat": 1726599000,
      "exp": 1726602600,
      "variant": "classical",
      "username": {
        "w": "boring_gnat",
        "b": "anonymous"
      },
      "elo": {
        "w": 1488,
        "b": null
      },
      "submode": "online",
      "color": "w",
      "min": "5",
      "increment": "3",
      "fen": "rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq -",
      "uid": "f445a35a",
      "jwt": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJhc3luYy5jaGVzc2xhYmxhYi5vcmciLCJpYXQiOjE3MjY1OTkwMDAsImV4cCI6MTcyNjYwMjYwMCwidmFyaWFudCI6ImNsYXNzaWNhbCIsInVzZXJuYW1lIjp7InciOiJib3JpbmdfZ25hdCIsImIiOiJhbm9ueW1vdXMifSwiZWxvIjp7InciOjE0ODgsImIiOm51bGx9LCJzdWJtb2RlIjoib25saW5lIiwiY29sb3IiOiJ3IiwibWluIjoiNSIsImluY3JlbWVudCI6IjMiLCJmZW4iOiJybmJxa2Juci9wcHBwcHBwcC84LzgvOC84L1BQUFBQUFBQL1JOQlFLQk5SIHcgS1FrcSAtIiwidWlkIjoiYWQyMzU4NmEifQ.Te7Bx3pY6z8o2WvX5YwM5Z-cmDJ4xAIPLAKzgr4Qzis"
    }
  ]
}
```
