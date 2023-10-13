# /inbox

## Description

Allows to play correspondence chess.

| Name | Description | Required |
| ---- | ----------- | -------- |
| `action` | `create`<br/>`read`<br/>`reply` | Yes |

## Usage

### Create an inbox

| Name | Description | Required |
| ---- | ----------- | -------- |
| `variant` | `960`<br/>`capablanca`<br/>`capablanca-fischer`<br/>`classical` | Yes |
| `add` | `fen`<br/>`startPos` | No |

```js
ws.send('/inbox create classical "{}"');
```

```text
{
  "/inbox": {
    "action": "create",
    "hash": "92a984a69050423e7b2e8dd2b9e99a22",
    "inbox": {
      "hash": "92a984a69050423e7b2e8dd2b9e99a22",
      "variant": "classical",
      "settings": [],
      "fen": "rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq -",
      "movetext": "",
      "createdAt": "2023-10-13 10:14:47",
      "createdBy": 306
    }
  }
}
```

### Read an inbox

| Name | Description | Required |
| ---- | ----------- | -------- |
| `hash` | The unique hash of the game. | Yes |

```js
ws.send('/inbox read 2624497eebedf72579717cd881400701');
```

```text
{
  "/inbox": {
    "action": "read",
    "inbox": {
      "hash": "2624497eebedf72579717cd881400701",
      "variant": "classical",
      "settings": [],
      "fen": "rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq -",
      "movetext": "",
      "createdAt": "2023-10-13 10:00:22",
      "createdBy": 249,
      "_id": 9
    }
  }
}
```

### Reply to a chess move

| Name | Description | Required |
| ---- | ----------- | -------- |
| `hash` | The unique hash of the game. | Yes |
| `move` | A chess move in PGN format. | Yes |

```js
ws.send('/inbox reply 2624497eebedf72579717cd881400701 "e4"');
```

```text
{
  "/inbox": {
    "action": "reply",
    "message": "Chess move successfully sent."
  }
}
```
