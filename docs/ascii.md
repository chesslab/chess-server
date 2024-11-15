# /ascii

Returns an ASCII representation of the board.

---

### Usage

#### Example

```js
ws.send('/start "{\\"variant\\":\\"classical\\",\\"mode\\":\\"analysis\\"}"');
ws.send('/play "{\\"color\\":\\"w\\",\\"pgn\\":\\"e4\\"}"');
ws.send('/ascii');
```

```text
{
  "/ascii": " r  n  b  q  k  b  n  r \n p  p  p  p  p  p  p  p \n .  .  .  .  .  .  .  . \n .  .  .  .  .  .  .  . \n .  .  .  .  P  .  .  . \n .  .  .  .  .  .  .  . \n P  P  P  P  .  P  P  P \n R  N  B  Q  K  B  N  R \n"
}
```
