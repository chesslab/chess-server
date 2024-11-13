# /accept

Accepts an invitation to play online with an opponent.

## `uid`

The unique identifier of the game.

## `username`

The username accepting the invitation.

## `elo`

The ELO of the user accepting the invitation.

---

### Usage

#### Example

```js
ws.send('accept "{\\"uid\\":\\"d07fa3d9\\",\\"username\\":\\"boring_gnat\\",\\"elo\\":1488}"');
```

```text
{
  "/accept": {
    "uid": "d07fa3d9",
    "jwt": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJhc3luYy5jaGVzc2xhYmxhYi5vcmciLCJpYXQiOjE3MjY1OTg3MTcsImV4cCI6MTcyNjYwMjMxNywidmFyaWFudCI6ImNsYXNzaWNhbCIsInVzZXJuYW1lIjp7InciOiJhbm9ueW1vdXMiLCJiIjoiYm9yaW5nX2duYXQifSwiZWxvIjp7InciOm51bGwsImIiOjE0ODh9LCJzdWJtb2RlIjoib25saW5lIiwiY29sb3IiOiJ3IiwibWluIjoiNSIsImluY3JlbWVudCI6IjMiLCJmZW4iOiJybmJxa2Juci9wcHBwcHBwcC84LzgvOC84L1BQUFBQUFBQL1JOQlFLQk5SIHcgS1FrcSAtIiwidWlkIjoiODg1MzU4YzIifQ.mUZdbBoi1bitZL8q-rO2Bt-KqdgGld8aGUTZCg-E-ss",
    "timer": {
      "w": 300,
      "b": 300
    },
    "startedAt": 1726598719
  }
}
```

Decoded JWT:

```text
{
  "iss": "async.chesslablab.org",
  "iat": 1726598717,
  "exp": 1726602317,
  "variant": "classical",
  "username": {
    "w": "anonymous",
    "b": "boring_gnat"
  },
  "elo": {
    "w": null,
    "b": 1488
  },
  "submode": "online",
  "color": "w",
  "min": "5",
  "increment": "3",
  "fen": "rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq -",
  "uid": "885358c2"
}
```
