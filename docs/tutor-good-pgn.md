# /tutor_good_pgn

Explains the why of a good move in terms of chess concepts.

---

### Usage

#### Example

Starts a classical game to play 1.e4 e5 2.f4 asking the chess tutor for a good move.

```js
ws.send('/start "{\\"variant\\":\\"classical\\",\\"mode\\":\\"analysis\\"}"');
ws.send('/play_lan "{\\"color\\":\\"w\\",\\"lan\\":\\"e2e4\\"}"');
ws.send('/play_lan "{\\"color\\":\\"b\\",\\"lan\\":\\"e7e5\\"}"');
ws.send('/play_lan "{\\"color\\":\\"w\\",\\"lan\\":\\"f2f4\\"}"');
ws.send('/tutor_good_pgn');
```

```text
{
  "/tutor_good_pgn": {
    "pgn": "c5",
    "paragraph": "White has a moderate control of the center. White has a slight space advantage. These pieces are hanging: The rook on a1, the rook on h1, the rook on a8, the rook on h8, the pawn on e4, the pawn on c5. Overall, 3 evaluation features are favoring White."
  }
}
```