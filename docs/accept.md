# /accept

Accepts an invitation to play online with an opponent.

## `settings`

### `hash`

The unique hash of the game.

### `username`

The username accepting the invitation.

---

## Usage

### Example

```js
ws.send('/accept "{\\"hash\\":\\"16539195\\",\\"username\\":\\"normal_magpie\\"}"');
```

```text
{
  "/accept": {
    "jwt": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJhc3luYy5jaGVzc2xhYmxhYi5vcmciLCJpYXQiOjE3MjU4NzcyNzAsImV4cCI6MTcyNTg4MDg3MCwidmFyaWFudCI6ImNsYXNzaWNhbCIsInVzZXJuYW1lIjp7InciOiJjb21wbGV0ZV9nbnUiLCJiIjoibm9ybWFsX21hZ3BpZSJ9LCJzdWJtb2RlIjoib25saW5lIiwiY29sb3IiOiJ3IiwibWluIjoiNSIsImluY3JlbWVudCI6IjMiLCJmZW4iOiJybmJxa2Juci9wcHBwcHBwcC84LzgvOC84L1BQUFBQUFBQL1JOQlFLQk5SIHcgS1FrcSAtIn0.DOgmcK-ergTtX1EaNMkDqZzbwDe2UGssPMJ3qH_w-10",
    "hash": "bbc88ff4",
    "timer": {
      "w": 300,
      "b": 300
    },
    "startedAt": 1725877310
  }
}
```

Decoded JWT:

```text
{
  "iss": "async.chesslablab.org",
  "iat": 1725877270,
  "exp": 1725880870,
  "variant": "classical",
  "username": {
    "w": "complete_gnu",
    "b": "normal_magpie"
  },
  "submode": "online",
  "color": "w",
  "min": "5",
  "increment": "3",
  "fen": "rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq -"
}
```
