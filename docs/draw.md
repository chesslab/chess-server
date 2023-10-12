# /draw

## Description

Allows to offer a draw.

## Parameters

| Name | Description | Required |
| ---- | ----------- | -------- |
| `action` | Accepted values are `propose`, `decline` and `accept`. | Yes |

## Usage

### Propose a draw

```js
ws.send('/draw propose');
```

```text
{
  "/draw": "propose"
}
```

### Decline a draw

```js
ws.send('/draw decline');
```

```text
{
  "/draw": "decline"
}
```

### Accept a draw

```js
ws.send('/draw accept');
```

```text
{
  "/draw": "accept"
}
```
