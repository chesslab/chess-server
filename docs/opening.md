# /opening

Opening results.

## `White`

The name of the player with the white pieces.

## `Black`

The name of the player with the black pieces.

## `Event`

The name of the event.

## `Result`

The result of the game.

---

### Usage

#### Example

```js
ws.send('/opening "{\\"White\\":\\"Anand,V\\",\\"Black\\":\\"Kasparov,G\\",\\"Event\\":\\"\\",\\"Result\\":\\"1-0\\"}"');
```

```text
{
  "/opening": [
    {
      "ECO": "B96",
      "total": 2
    }
  ]
}
```
