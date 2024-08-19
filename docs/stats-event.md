# /stats_event

Openings by event.

## `settings`

The settings as per these options.

- `Event` is the name of the event.
- `Result` is the result of the game.

---

## Usage

### Example

```js
ws.send('/stats_event "{\\"Event\\":\\"FIDE Candidates 2016\\",\\"Result\\":\\"1-0\\"}"');
```

```text
{
  "/stats_event": [
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
