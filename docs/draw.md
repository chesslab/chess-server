# /draw

Offers a draw.

## `action`

The action to take as per these options.

- `propose`
- `decline`
- `accept`

---

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
