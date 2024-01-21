# /takeback

Allows to take back a move.

| Name | Description | Required |
| ---- | ----------- | -------- |
| `action` | `propose`<br/>`decline`<br/>`accept` | Yes |

## Usage

### Propose a take back

```js
ws.send('/takeback propose');
```

```text
{
  "/takeback": {
    "action": "propose"
   }
}
```

### Decline a take back

```js
ws.send('/takeback decline');
```

```text
{
  "/takeback": {
    "action": "decline"
   }
}
```

### Accept a take back

```js
ws.send('/takeback accept');
```

```text
{
  "/takeback": {
    "action": "accept"
   }
}
```
