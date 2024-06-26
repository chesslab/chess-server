# /restart

Restarts an existing game.

## `hash`

The unique hash of the game.

---

## Usage

### Example

```js
ws.send('/restart cf897a92');
```

```text
{
  "/restart": {
    "jwt": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJhc3luYy5jaGVzc2xhYmxhYi5vcmciLCJpYXQiOjE3MTYzOTMzOTksImV4cCI6MTcxNjM5Njk5OSwidmFyaWFudCI6ImNsYXNzaWNhbCIsInN1Ym1vZGUiOiJvbmxpbmUiLCJjb2xvciI6InciLCJtaW4iOiI1IiwiaW5jcmVtZW50IjoiMyIsImZlbiI6InJuYnFrYm5yL3BwcHBwcHBwLzgvOC84LzgvUFBQUFBQUFAvUk5CUUtCTlIgdyBLUWtxIC0ifQ.-i3o-ODk7HF_ifuwiXLzpP5Itw12QTB07XeqbEQdULM",
    "hash": "72647a10",
    "timer": {
      "w": 300,
      "b": 300
    }
  }
}
```
