# /draw

## Description

Allows to draw a game.

## Parameters

| Name | Description | Required |
| ---- | ----------- | -------- |
| action | Accepts: propose, accept | Yes |

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
