# /randomizer

Starts a random position.

## `turn`

The color to move in the starting position, as per these options.

- `w` for white to move.
- `b` for black to move.

## `items`

The piece composition string for both colors (excluding the king), as per these options for each color (`w` and `b`).

- `P` Pawn
- `Q` Queen
- `R` Rook
- `BB` Bishop pair
- `BN` Bishop and Knight
- `QR` Queen and Rook

---

## Usage

### Get a random position with white to move; King and queen and rook vs. king and rook

```js
ws.send('/randomizer w "{\\"w\":\\"QR\",\\"b\\":\\"R\\"}"');
```

```text
{
  "/randomizer": {
    "turn": "w",
    "fen": "8/8/8/8/Q3r3/8/7k/2R2K2 w - -"
  }
}
```

### Get a random position with black to move; King and rook vs. king

```js
ws.send('/randomizer b "{\\"b\":\\"R\\"}"');
```

```text
{
  "/randomizer": {
    "turn": "b",
    "fen": "k7/8/8/8/8/6K1/8/3r4 b - -"
  }
}
```

A new game can be started from the recieved data

```js
ws.send('/start classical stockfish "{\\"color\\":\\"b\\",\\"fen\":\\"k7/8/8/8/8/6K1/8/3r4 b - -\\"}"');
```
