# /leave

Allows to leave a game.

## `action`

The action to take as per these options.

- `accept`

---

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
