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
    "jwt": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJwY2hlc3MubmV0IiwiaWF0IjoxNjk0NDQxOTcxLCJleHAiOjE2OTQ0NDU1NzEsInZhcmlhbnQiOiJjbGFzc2ljYWwiLCJzdWJtb2RlIjoiZnJpZW5kIiwiY29sb3IiOiJ3IiwibWluIjo1LCJpbmNyZW1lbnQiOjMsImZlbiI6InJuYnFrYm5yL3BwcHBwcHBwLzgvOC84LzgvUFBQUFBQUFAvUk5CUUtCTlIgdyBLUWtxIC0ifQ.POuK_cR3U_bblLa8LFyGg1AJEE5_iW_AquuNn7K4qHI",
    "hash": "e69e3228e22dbcab5c2274646ae9a23647b222d084e26dea3216016d026f7108",
    "timer": {
      "w": 300,
      "b": 300
    },
    "startedAt": 1694441992
  }
}
```
