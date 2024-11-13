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

### Usage

#### Example

Get a random position with white to move; King and queen and rook vs. king and rook.

```js
ws.send('/randomizer "{\\"turn\\":\\"w\\",\\"items\\":{\\"w\\":\\"QR\\",\\"b\\":\\"R\\"}}"');
```

```text
{
  "/randomizer": {
    "turn": "w",
    "fen": "1K1R4/8/4k3/8/3Q4/r7/8/8 w - -"
  }
}
```
