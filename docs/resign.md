# /resign

Allows to resign a game.

## `action`

The action to take as per these options.

- `accept`

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
