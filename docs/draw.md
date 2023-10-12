# /draw

## Description

Allows to offer a draw.

## Parameters

| Name | Description | Required |
| ---- | ----------- | -------- |
| `action` | `propose`<br/>`decline`<br/>`accept` | Yes |

## Usage

### Propose a draw

```js
ws.send('/draw propose');
```

```text
{
  "/draw": {
    "action": "propose"
   }
}
```

### Decline a draw

```js
ws.send('/draw decline');
```

```text
{
  "/draw": {
    "action": "decline"
   }
}
```

### Accept a draw

```js
ws.send('/draw accept');
```

```text
{
  "/takeback": {
    "action": "accept"
   }
}
```
