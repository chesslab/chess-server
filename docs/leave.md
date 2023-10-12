# /leave

## Description

Allows to leave a game.

## Parameters

| Name | Description | Required |
| ---- | ----------- | -------- |
| `action` | Accepted values:<br/><br/><ul><li>`accept`</li></ul> | Yes |

## Usage

### Example

```js
ws.send('/leave accept');
```

```text
{
  "/leave": {
    "action": "accept"
   }
}
```
