# /result_player

Openings results by player.

## `params`

### `White`

The name of the player with the white pieces.

### `Black`

The name of the player with the black pieces.

### `Result`

The result of the game.

---

## Usage

### Example

```js
ws.send('/result_player "{\\"White\\":\\"Anand,V\\",\\"Black\\":\\"Kasparov,G\\",\\"Result\\":\\"1-0\\"}"');
```

```text
{
  "/result_player": [
    {
      "ECO": "B96",
      "total": 2
    }
  ]
}
```
