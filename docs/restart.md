# /restart

Restarts an existing game.

| Name | Description | Required |
| ---- | ----------- | -------- |
| `hash` | The unique hash of the game. | Yes |

## Usage

### Example

```js
ws.send('/restart ffc536a8f44fc21b4d254e4fb85d7e33');
```

```text
{
  "/restart": {
    "jwt": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJwY2hlc3MubmV0IiwiaWF0IjoxNjk0MTAxOTI2LCJleHAiOjE2OTQxMDU1MjYsInZhcmlhbnQiOiJjbGFzc2ljYWwiLCJzdWJtb2RlIjoib25saW5lIiwiY29sb3IiOiJ3IiwibWluIjo1LCJpbmNyZW1lbnQiOjMsImZlbiI6InJuYnFrYm5yL3BwcHBwcHBwLzgvOC84LzgvUFBQUFBQUFAvUk5CUUtCTlIgdyBLUWtxIC0ifQ.sE6Is9GYf0R6l0_C8rt7VPE8fVChsYlb9teEQw_2QUQ",
    "hash": "5665d2e6c84344db95aa9fdbb3bc196b88238bce22a1c58a41f2f269deee6c33",
    "timer": {
      "w": 300,
      "b": 300
    }
  }
}
```
