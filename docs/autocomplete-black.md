# /autocomplete_black

Autocomplete data for chess players.

---

## Usage

### Example

```js
ws.send('/autocomplete_black "{\\"Black\\":\\"kas\\"}"');
```

```text
{
  "/autocomplete_black": [
    "Kasparov, Gary",
    "Malisauskas, Vidmantas",
    "Kasimdzhanov, Rustam",
    "Kasparov,G",
    "Kasimdzhanov,R",
    "Kashdan, Isaac",
    "Eliskases, Erich Gottlieb",
    "Kas Fromm, Rita",
    "Pourkashiyan,A",
    "Ladanyike Karakas, Eva"
  ]
}
```
