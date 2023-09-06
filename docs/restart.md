# /restart

Restarts a game.

## Detailed Description

The `/restart` command allows you to restart an existing game. You can either specify the unique game hash to restart a specific game or ommit it to restart the current game.

## Parameters

| Name | Description | Required |
| ---- | ----------- | -------- |
| Hash | The unique hash of the game. | No |

## Usage

### Restart a game [Without hash]

```js
ws.send('/restart');
```

### Restart a game [With hash]

Assuming that a game was started and has the following hash: `"hash": "0151f4d3af859a6aa14abd3e0ee57f2a"`, restarting it could be done like so:

```js
ws.send('/restart {"hash":"0151f4d3af859a6aa14abd3e0ee57f2a"}');
```
