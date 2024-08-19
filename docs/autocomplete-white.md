# /autocomplete_white

Autocomplete data for chess players.

## `settings`

The name of the player with the white pieces.

- `White`

---

## Usage

### Example

```js
ws.send('/autocomplete_white "{\\"White\\":\\"kas\\"}"');
```

```text
{
  "/autocomplete_white": [
    "Kasparov, Gary",
    "Kasimdzhanov, Rustam",
    "Kasparov,G",
    "Kasimdzhanov,R",
    "Eliskases, Erich Gottlieb",
    "Kashefi,Amir Hosein",
    "Sadvakasov, Darmen",
    "Kashlinskaya,A",
    "Kasioura, Froso",
    "Kasoshvili, Tsiala"
  ]
}
```
