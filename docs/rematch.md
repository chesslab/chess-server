# /rematch

## Description

Allows to offer a rematch.

## Parameters

| Name | Description | Required |
| ---- | ----------- | -------- |
| `action` | Accepted values:<br/><br/>`propose`<br/>`decline`<br/>`accept` | Yes |

## Usage

### Propose a rematch

```js
ws.send('/rematch propose');
```

```text
{
  "/rematch": {
    "action": "propose"
   }
}
```

### Decline a rematch

```js
ws.send('/rematch decline');
```

```text
{
  "/rematch": {
    "action": "decline"
   }
}
```

### Accept a rematch

```js
ws.send('/rematch accept');
```

```text
{
  "/rematch": {
    "action": "accept"
   }
}
```
