# /randomizer

Starts a random position.

## `turn`

The color as per these options.

- `w` for the white pieces.
- `b` for the black pieces.

## `items`

The piece composition string as per these options.

- `P` Pawn
- `Q` Queen
- `R` Rook
- `BB` Bishop pair
- `BN` Bishop and Knight
- `QR` Queen and Rook

---

## Usage

### Example

Get a random position with white to move; King and queen and rook vs. king and rook.

```js
ws.send('/randomizer w "{\\"w\\":\\"QR\\",\\"b\\":\\"R\\"}"');
```

```text
{
  "/randomizer": {
    "turn": "w",
    "fen": "8/4K3/1R6/2Q5/5k2/8/8/6r1 w - -"
  }
}
```
