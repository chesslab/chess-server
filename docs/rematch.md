# /rematch

Offers a rematch.

## `action`

The action to take as per these options.

- `propose`
- `decline`
- `accept`

---

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
