# /legal_sqs

Returns the legal squares of a piece.

## Parameters

| Name | Description | Required |
| ---- | ----------- | -------- |
| position | The location of the piece on the board. | Yes |

### Example

Starts a classical game to find out the legal squares of the piece on e2.

```js
ws.send('/start classical analysis');
ws.send('/legal_sqs e2');
```

```text
{
  "/legal_sqs": {
    "color": "w",
    "id": "P",
    "sqs": [
      "e3",
      "e4"
    ]
  }
}
```
