# /restart

Restarts an existing game.

## `settings`

### `hash`

The unique hash of the game.

---

## Usage

### Example

```js
ws.send('/restart "{\\"hash\\":\\"2a8e9850\\"}"');
```

```text
{
  "/restart": {
    "jwt": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJhc3luYy5jaGVzc2xhYmxhYi5vcmciLCJpYXQiOjE3MjU4ODAyODcsImV4cCI6MTcyNTg4Mzg4NywidmFyaWFudCI6Ijk2MCIsInVzZXJuYW1lIjp7InciOiJub3JtYWxfbWFncGllIiwiYiI6ImNvbXBsZXRlX2dudSJ9LCJzdWJtb2RlIjoib25saW5lIiwiY29sb3IiOiJ3IiwibWluIjoiNSIsImluY3JlbWVudCI6IjMiLCJmZW4iOiJucmtxcm5iYi9wcHBwcHBwcC84LzgvOC84L1BQUFBQUFBQL05SS1FSTkJCIHcgS1FrcSAtIiwic3RhcnRQb3MiOiJOUktRUk5CQiJ9.EpQRoOb0cmua300nFgpwkUaOTDlrL1wU1nzC-uEuKz8",
    "hash": "7639982e",
    "timer": {
      "w": 300,
      "b": 300
    }
  }
}
```
