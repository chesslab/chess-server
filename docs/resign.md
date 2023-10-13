# /resign

Allows to resign a game.

| Name | Description | Required |
| ---- | ----------- | -------- |
| `action` | `accept` | Yes |

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
