# /accept

Accepts an invitation to play online with an opponent.

### `hash`

The unique hash of the game.

## Usage

### Example

```js
ws.send('/accept 876d7a7b');
```

```text
{
  "/accept": {
    "jwt": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJhc3luYy5jaGVzc2xhYmxhYi5vcmciLCJpYXQiOjE3MTYzOTk3NjYsImV4cCI6MTcxNjQwMzM2NiwidmFyaWFudCI6ImNsYXNzaWNhbCIsInN1Ym1vZGUiOiJmcmllbmQiLCJjb2xvciI6InciLCJtaW4iOiI1IiwiaW5jcmVtZW50IjoiMyIsImZlbiI6InJuYnFrYm5yL3BwcHBwcHBwLzgvOC84LzgvUFBQUFBQUFAvUk5CUUtCTlIgdyBLUWtxIC0ifQ.hqxBC0gZS7aka7_FUGtLJ3uFG_RtjGqhHi7ttvHb73A",
    "hash": "876d7a7b",
    "timer": {
      "w": 300,
      "b": 300
    },
    "startedAt": 1716399810
  }
}
```
