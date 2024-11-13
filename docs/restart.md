# /restart

Restarts an existing game.

## `jwt`

The JWT token of the game.

---

### Usage

#### Example

```js
ws.send('/restart "{\\"jwt\\":\\"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJhc3luYy5jaGVzc2xhYmxhYi5vcmciLCJpYXQiOjE3MjY1MDI2ODIsImV4cCI6MTcyNjUwNjI4MiwidmFyaWFudCI6ImNsYXNzaWNhbCIsInVzZXJuYW1lIjp7InciOiJhbm9ueW1vdXMiLCJiIjoiYW5vbnltb3VzIn0sImVsbyI6eyJ3IjpudWxsLCJiIjpudWxsfSwic3VibW9kZSI6Im9ubGluZSIsImNvbG9yIjoidyIsIm1pbiI6IjUiLCJpbmNyZW1lbnQiOiIzIiwiZmVuIjoicm5icWtibnIvcHBwcHBwcHAvOC84LzgvOC9QUFBQUFBQUC9STkJRS0JOUiB3IEtRa3EgLSJ9.AqpumQte3WScJBlE8PrGjp_SzYQUfmRD7JPvvzW0eXQ\\"}"');
```

```text
{
  "/restart": {
    "jwt": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJhc3luYy5jaGVzc2xhYmxhYi5vcmciLCJpYXQiOjE3MjY1MDI3MDYsImV4cCI6MTcyNjUwNjMwNiwidmFyaWFudCI6ImNsYXNzaWNhbCIsInVzZXJuYW1lIjp7InciOiJhbm9ueW1vdXMiLCJiIjoiYW5vbnltb3VzIn0sImVsbyI6eyJ3IjpudWxsLCJiIjpudWxsfSwic3VibW9kZSI6Im9ubGluZSIsImNvbG9yIjoidyIsIm1pbiI6IjUiLCJpbmNyZW1lbnQiOiIzIiwiZmVuIjoicm5icWtibnIvcHBwcHBwcHAvOC84LzgvOC9QUFBQUFBQUC9STkJRS0JOUiB3IEtRa3EgLSJ9.OumjF8qCQ_DgPn07sKxd3hoJKc5w4h84iw-qwBp8zro",
    "timer": {
      "w": 300,
      "b": 300
    }
  }
}
```
