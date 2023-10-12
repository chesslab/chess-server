# /resign

## Description

Allows to resign a game.

## Parameters

| Name | Description | Required |
| ---- | ----------- | -------- |
| `action` | Accepted values:<br/><br/><ul><li>`accept`</li></ul> | Yes |

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
