# /extract

Extracts knowledge from a game by using the Steinitz evaluation.

## `variant`

The chess variant as per these options.

- `classical` chess, also known as standard or slow chess.
- `960` is the same as classical chess except that the starting position of the pieces is randomized.
- `dunsany` is an asymmetric variant in which Black has the standard chess army and White has 32 pawns.
- `losing` chess, the objective of each player is to lose all of their pieces or be stalemated.
- `racing-kings` consists of being the first player to move their king to the eighth row.
- `capablanca` incorporates two new pieces, the archbishop and the chancellor, and is played on a 10×8 board.
- `capablanca-fischer` is a mix of Capablanca chess and Chess960.

## `movetext`

The sequence of moves played in the game.

---

### Usage

#### Example

```js
ws.send('/extract "{\"variant\":\"classical\",\"movetext\":\"1.e4 e5 2.Nf3 Nc6 3.Bc4\"}"');
```

```text
{
  "/extract": [
    0,
    0.5,
    0,
    1,
    0.25,
    1
  ]
}
```
