# /restart

## Description

Allows to restart an existing game.

## Parameters

| Name | Description | Required |
| ---- | ----------- | -------- |
| hash | The unique hash of the game. | Yes |

## Usage

### Restart a game

```js
ws.send('/restart ffc536a8f44fc21b4d254e4fb85d7e33');
```

```text
{
  "/restart": {
    "jwt": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJwY2hlc3MubmV0IiwiaWF0IjoxNjk0MTAxOTI2LCJleHAiOjE2OTQxMDU1MjYsInZhcmlhbnQiOiJjbGFzc2ljYWwiLCJzdWJtb2RlIjoib25saW5lIiwiY29sb3IiOiJ3IiwibWluIjo1LCJpbmNyZW1lbnQiOjMsImZlbiI6InJuYnFrYm5yL3BwcHBwcHBwLzgvOC84LzgvUFBQUFBQUFAvUk5CUUtCTlIgdyBLUWtxIC0ifQ.sE6Is9GYf0R6l0_C8rt7VPE8fVChsYlb9teEQw_2QUQ",
    "hash": "23bb4c7ec2e5a33f436b41376bb41064",
    "timer": {
      "w": 300,
      "b": 300
    }
  }
}
```
