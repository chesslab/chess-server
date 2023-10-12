# /draw

## Description

Allows to offer a draw.

## Parameters

| Name | Description | Required |
| ---- | ----------- | -------- |
| `action` | Accepted values are `propose` and `accept`. | Yes |

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

### Accept a draw

```js
ws.send('/draw accept');
```

```text
{
  "/draw": "accept"
}
```
