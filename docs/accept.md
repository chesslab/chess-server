# /accept

Accepts an invitation to play online with an opponent.

| Name | Description | Required |
| ---- | ----------- | -------- |
| `hash` | The unique hash of the game. | Yes |

## Usage

### Example

```js
ws.send('/accept e69e3228e22dbcab5c2274646ae9a23647b222d084e26dea3216016d026f7108');
```

```text
{
  "/accept": {
    "jwt": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJhc3luYy5jaGVzc2xhYmxhYi5vcmciLCJpYXQiOjE3MTYzOTMxOTQsImV4cCI6MTcxNjM5Njc5NCwidmFyaWFudCI6ImNsYXNzaWNhbCIsInN1Ym1vZGUiOiJvbmxpbmUiLCJjb2xvciI6ImIiLCJtaW4iOjUsImluY3JlbWVudCI6MywiZmVuIjoicm5icWtibnIvcHBwcHBwcHAvOC84LzgvOC9QUFBQUFBQUC9STkJRS0JOUiB3IEtRa3EgLSJ9.uEVe0vMgOroQCKqTtXqvFZvTidHlESeaVqQXj7_FcdA",
    "hash": "74cf7843",
    "timer": {
      "w": 300,
      "b": 300
    },
    "startedAt": 1716393248
  }
}
```
