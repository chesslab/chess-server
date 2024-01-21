# /accept

Accepts an invitation to play online with an opponent.

| Name | Description | Required |
| ---- | ----------- | -------- |
| `hash` | The unique hash of the game. | Yes |

## Usage

### Example

```js
ws.send('/accept 15f78c0035a719491b89522b4905a490');
```

```text
{
  "/accept": {
    "jwt": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJwY2hlc3MubmV0IiwiaWF0IjoxNjk0NDQxOTcxLCJleHAiOjE2OTQ0NDU1NzEsInZhcmlhbnQiOiJjbGFzc2ljYWwiLCJzdWJtb2RlIjoiZnJpZW5kIiwiY29sb3IiOiJ3IiwibWluIjo1LCJpbmNyZW1lbnQiOjMsImZlbiI6InJuYnFrYm5yL3BwcHBwcHBwLzgvOC84LzgvUFBQUFBQUFAvUk5CUUtCTlIgdyBLUWtxIC0ifQ.POuK_cR3U_bblLa8LFyGg1AJEE5_iW_AquuNn7K4qHI",
    "hash": "15f78c0035a719491b89522b4905a490",
    "timer": {
      "w": 300,
      "b": 300
    },
    "startedAt": 1694441992
  }
}
```
