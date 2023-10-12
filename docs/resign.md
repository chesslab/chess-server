# /resign

## Description

Allows to resign a game.

## Parameters

| Name | Description | Required |
| ---- | ----------- | -------- |
| `action` | Accepted values:<br/><br/>`accept` | Yes |

## Usage

### Example

```js
ws.send('/resign accept');
```

```text
{
  "/resign": {
    "action": "accept"
   }
}
```
