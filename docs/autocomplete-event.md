# /autocomplete_event

Autocomplete data for chess events.

## `Event`

The name of the event.

---

### Usage

#### Example

```js
ws.send('/autocomplete_event "{\\"Event\\":\\"wor\\"}"');
```

```text
{
  "/autocomplete_event": [
    "PCA-World Championship",
    "GSM World Blitz Cup",
    "FIDE World Cup Gp D",
    "FIDE World Cup KO",
    "FIDE World Cup Gp C",
    "FIDE World Cup QF",
    "FIDE World Cup SF",
    "FIDE World Cup Final",
    "Basque Country vs. World Rapid",
    "Basque Country vs. World Blindfold"
  ]
}
```
