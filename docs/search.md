# /search

Finds up to 25 games matching the criteria.

## `Event`

The name of the event.

## `Date`

The year the game was played.

## `White`

The name of the player with the white pieces.

## `Black`

The name of the player with the black pieces.

## `Result`

The result of the game.

## `ECO`

The ECO code of the opening used in the game.

## `movetext`

The sequence of moves played in the game.

---

### Usage

#### Example

```js
ws.send('/search "{\\"Event\\":\\"\\",\\"Date\\":\\"2023\\",\\"White\\":\\"\\",\\"Black\\":\\"\\",\\"Result\\":\\"0-1\\",\\"ECO\\":\\"\\",\\"movetext\\":\\"\\"}"');
```

```text
{
  "/search": [
    {
      "id": 10098,
      "Event": "Titled Tue 12th Sep Late",
      "Site": "chess.com INT",
      "Date": "2023.09.12",
      "White": "Deac,Bogdan-Daniel",
      "Black": "Carlsen,M",
      "Result": "0-1",
      "WhiteElo": "2702",
      "BlackElo": "2839",
      "ECO": "D30",
      "FEN": null,
      "movetext": "1.d4 d5 2.c4 e6 3.Nf3 c6 4.Nbd2 Nf6 5.g3 c5 6.cxd5 exd5 7.Bg2 Nc6 8.O-O h6 9.Re1 cxd4 10.Nb3 Bd6 11.Nbxd4 O-O 12.b3 Re8 13.Bb2 Bb4 14.Rf1 Nxd4 15.Qxd4 Ba5 16.Qd3 Bb6 17.Rad1 Be6 18.Nd4 Bg4 19.Rfe1 Rc8 20.h3 Bh5 21.g4 Bg6 22.Nf5 h5 23.Bf3 hxg4 24.hxg4 Ne4 25.Bxe4 Rxe4 26.Qxe4 dxe4 27.Rxd8+ Rxd8 28.Bc3 Bxf5 29.gxf5 Rd5 30.f6 g5 31.e3 Kh7 32.b4 Kg6 33.Rc1 a6 34.a4 Bc7 35.Kg2 Bd6 36.Bd4 Bxb4 37.Rc7 b5 38.axb5 axb5 39.Rb7 Bf8 40.f3 exf3+ 41.Kxf3 b4 42.Kg4 Bc5 43.Bxc5 Rxc5 44.Rxb4 Re5 45.e4 Kxf6 46.Rb6+ Re6 47.Rb4 Kg6 48.Rc4 Rxe4+ 49.Rxe4 f5+"
    },
    ...
    {
      "id": 9456,
      "Event": "Titled Tue 31st Jan Late",
      "Site": "chess.com INT",
      "Date": "2023.01.31",
      "White": "Sanchez,Robert",
      "Black": "Carlsen,M",
      "Result": "0-1",
      "WhiteElo": "1971",
      "BlackElo": "2859",
      "ECO": "B46",
      "FEN": null,
      "movetext": "1.e4 c5 2.Nf3 e6 3.d4 cxd4 4.Nxd4 Nc6 5.Nc3 a6 6.Nxc6 bxc6 7.Bd3 d5 8.O-O Nf6 9.Qe2 Bb7 10.f4 Be7 11.e5 Nd7 12.f5 exf5 13.Bxf5 O-O 14.Bf4 Nc5 15.Qg4 g6 16.Bh6 Qb6 17.Kh1 Qxb2 18.Bxf8 Rxf8 19.Qg3 d4 20.Ne4 Nxe4 21.Bxe4 Bc8 22.Rab1 Qxa2 23.Rb8 Be6 24.Rxf8+ Bxf8 25.h4 Qa5 26.h5 Bg7 27.hxg6 hxg6 28.Bxg6 fxg6 29.Qxg6 Qxe5 30.Qe8+ Kh7 31.Rf3 Qe1+ 32.Kh2 Be5+ 33.g3 Qe2+"
    }
  ]
}
```
