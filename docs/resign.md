# /resign

## Description

Allows to resign a game.

## Parameters

| Name | Description | Required |
| ---- | ----------- | -------- |
| `action` | Accepted values are `accept`. | Yes |

## Usage

### Example

```js
ws.send('/resign accept');
```

```text
{
  "/resign": "accept"
}
```
