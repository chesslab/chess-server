# /takeback

Takes back a move as per these actions.

- `propose`
- `decline`
- `accept`

---

### Usage

### Propose a take back

```js
ws.send('/takeback propose');
```

```text
{
  "/takeback": {
    "action": "propose"
   }
}
```

### Decline a take back

```js
ws.send('/takeback decline');
```

```text
{
  "/takeback": {
    "action": "decline"
   }
}
```

### Accept a take back

```js
ws.send('/takeback accept');
```

```text
{
  "/takeback": {
    "action": "accept"
   }
}
```
