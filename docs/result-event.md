# /result_event

Openings results by event.

## `settings`

### `Event`

The name of the event.

### `Result`

The result of the game.

---

## Usage

### Example

```js
ws.send('/result_event "{\\"Event\\":\\"FIDE Candidates 2016\\",\\"Result\\":\\"1-0\\"}"');
```

```text
{
  "/result_event": [
    {
      "ECO": "C65",
      "total": 2
    },
    {
      "ECO": "A29",
      "total": 2
    },
    {
      "ECO": "A06",
      "total": 1
    },
    {
      "ECO": "C88",
      "total": 1
    },
    {
      "ECO": "C50",
      "total": 1
    }
  ]
}
```
