# /takeback

## Description

Allows to take back a move.

## Parameters

| Name | Description | Required |
| ---- | ----------- | -------- |
| `action` | Accepted values are `propose`, `decline` and `accept`. | Yes |

## Usage

### Propose a take back

```js
ws.send('/takeback propose');
```

```text
{
  "/takeback": "propose"
}
```

### Decline a take back

```js
ws.send('/takeback decline');
```

```text
{
  "/takeback": "decline"
}
```

### Accept a take back

```js
ws.send('/takeback accept');
```

```text
{
  "/takeback": "accept"
}
```
