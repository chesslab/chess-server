# /play_rav

Plays the moves in a RAV movetext.

## `variant`

The chess variant as per these options.

- `classical` chess, also known as standard or slow chess.
- `960` is the same as classical chess except that the starting position of the pieces is randomized.
- `dunsany` is an asymmetric variant in which Black has the standard chess army and White has 32 pawns.
- `losing` chess, the objective of each player is to lose all of their pieces or be stalemated.
- `racing-kings` consists of being the first player to move their king to the eighth row.

## `movetext`

The sequence of moves played in the game.

---

### Usage

#### Example

```js
ws.send('/play_rav "{\\"variant\\":\\"classical\\",\\"movetext\\":\\"{ Adapted notes, originally by J. R. Capablanca. } 1.d4 d5 2.Nf3 e6 3.c4 Nf6 4.Bg5 Be7 5.e3 Nbd7 6.Nc3 O-O 7.Rc1 b6 8.cxd5 exd5 9.Bb5 {is a new move which has no merit outside of its novelty. I played it for the first time against Teichmann in Berlin in 1913. } (9.Bd3 { is the normal move but Qa4 may be the best, after all. }) 9...Bb7 10.Qa4 a6 (10...c5 { is the proper continuation.}) 11.Bxd7 Nxd7 12.Bxe7 Qxe7 13.Qb3 { with the idea of preventing c5, but still better would have been to castle.} Qd6 (13...c5 { could be played as well. Black would come out all right from the many complications arising from this move.}) 14.O-O Rfd8 15.Rfd1 Rab8 16.Ne1 { in order to draw the knight away from the line of the bishop, which would soon be open, as it actually occurred in the game.} Nf6 17.Rc2 c5 18.dxc5 bxc5 19.Ne2 Ne4 (19...Ng4 { begins a failed attack. }) (19...d4 { begins a failed attack. }) 20.Qa3 Rbc8 21.Ng3 Nxg3 22.hxg3 Qb6 23.Rcd2 (23.Rdc1 { would not have been better because of d4, etc. } d4) 23...h6 24.Nf3 d4 25.exd4 Bxf3 26.Qxf3 Rxd4 27.Rc2 Rxd1+ 28.Qxd1 Rd8 29.Qe2 Qd6 30.Kh2 Qd5 31.b3 Qf5 32.g4 Qg5 33.g3 Rd6 { is unquestionably the best move; with any other move Black would, perhaps, have found it impossible to draw.} 34.Kg2 g6 35.Qc4 Re6 36.Qxc5 Qxg4 37.f3 Qg5 38.Qxg5 hxg5 39.Kf2 Rd6 40.Ke3 Re6+ 41.Kd4 Rd6+ (42.Kc5 { is too risky. The way to win was not at all clear and I even thought that with that move Black might win. }) 42.Ke3 Re6+ 43.Kf2 Rd6 44.g4 Rd1 (45.Ke3 { is the right move to make. It is perhaps the only chance White has to win, or at least come near it. } Ra1 46.Kd4 { gains an important move. } Kg7 47.b4 Rf1 { accomplishes nothing with the white king on d4. }) 45.Ke2 { was played instead. } Ra1 46.Kd3 Kg7 47.b4 Rf1 { was the best move with the white king on d3. } 48.Ke3 { and the remainder of the game needs no comments. } Rb1 49.Rc6 Rxb4 50.Rxa6 Rb2 1/2-1/2\\"}"');
```

```text
{
  "/play_rav": {
    "variant": "classical",
    "turn": "w",
    "filtered": "{Adapted notes, originally by Robert James Fischer from a television interview.} 1.d4 d5 2.c4 e6 3.Nc3 Nf6 4.Bg5 Be7 5.Nf3 O-O 6.c5 {is a mistake already; instead it should be played e3, naturally.} 6...b6 7.b4 bxc5 8.dxc5 a5 9.a3 d4 {is a fantastic move; it's the winning move. The pawn can't be taken with the knight because of axb4.} 10.Bxf6 gxf6 11.Na4 e5 {because the center is easily winning. Black's kingside weakness is nothing.} 12.b5 Be6 {with the idea of dominating the game with a powerful mobile center.} 13.g3 c6 14.bxc6 Nxc6 15.Bg2 Rb8 {threatening Bb3.} 16.Qc1 d3 17.e3 e4 18.Nd2 f5 19.O-O Re8 {is a very modern move; a quiet positional move. The rook is doing nothing now, but later...} 20.f3 {to break up the center, it's the only chance for White.} 20...Nd4 21.exd4 Qxd4+ 22.Kh1 e3 (22...Qxa4 {allows Black to easily regain material.}) 23.Nc3 Bf6 24.Ndb1 d2 25.Qc2 Bb3 26.Qxf5 d1=Q 27.Nxd1 Bxd1 28.Nc3 e2 29.Raxd1 Qxc3 {and White resigns. The center has prevailed.}",
    "movetext": " 1.d4 d5 2.c4 e6 3.Nc3 Nf6 4.Bg5 Be7 5.Nf3 O-O 6.c5 b6 7.b4 bxc5 8.dxc5 a5 9.a3 d4 10.Bxf6 gxf6 11.Na4 e5 12.b5 Be6 13.g3 c6 14.bxc6 Nxc6 15.Bg2 Rb8 16.Qc1 d3 17.e3 e4 18.Nd2 f5 19.O-O Re8 20.f3 Nd4 21.exd4 Qxd4+ 22.Kh1 e3 23.Nc3 Bf6 24.Ndb1 d2 25.Qc2 Bb3 26.Qxf5 d1=Q 27.Nxd1 Bxd1 28.Nc3 e2 29.Raxd1 Qxc3 ",
    "breakdown": [
      "{Adapted notes, originally by Robert James Fischer from a television interview.} 1.d4 d5 2.c4 e6 3.Nc3 Nf6 4.Bg5 Be7 5.Nf3 O-O 6.c5 {is a mistake already; instead it should be played e3, naturally.} 6...b6 7.b4 bxc5 8.dxc5 a5 9.a3 d4 {is a fantastic move; it's the winning move. The pawn can't be taken with the knight because of axb4.} 10.Bxf6 gxf6 11.Na4 e5 {because the center is easily winning. Black's kingside weakness is nothing.} 12.b5 Be6 {with the idea of dominating the game with a powerful mobile center.} 13.g3 c6 14.bxc6 Nxc6 15.Bg2 Rb8 {threatening Bb3.} 16.Qc1 d3 17.e3 e4 18.Nd2 f5 19.O-O Re8 {is a very modern move; a quiet positional move. The rook is doing nothing now, but later...} 20.f3 {to break up the center, it's the only chance for White.} 20...Nd4 21.exd4 Qxd4+ 22.Kh1 e3",
      "22...Qxa4 {allows Black to easily regain material.}",
      "23.Nc3 Bf6 24.Ndb1 d2 25.Qc2 Bb3 26.Qxf5 d1=Q 27.Nxd1 Bxd1 28.Nc3 e2 29.Raxd1 Qxc3 {and White resigns. The center has prevailed.}"
    ],
    "fen": [
      "rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq -",
      "rnbqkbnr/pppppppp/8/8/3P4/8/PPP1PPPP/RNBQKBNR b KQkq d3",
      "rnbqkbnr/ppp1pppp/8/3p4/3P4/8/PPP1PPPP/RNBQKBNR w KQkq d6",
      "rnbqkbnr/ppp1pppp/8/3p4/2PP4/8/PP2PPPP/RNBQKBNR b KQkq c3",
      "rnbqkbnr/ppp2ppp/4p3/3p4/2PP4/8/PP2PPPP/RNBQKBNR w KQkq -",
      "rnbqkbnr/ppp2ppp/4p3/3p4/2PP4/2N5/PP2PPPP/R1BQKBNR b KQkq -",
      "rnbqkb1r/ppp2ppp/4pn2/3p4/2PP4/2N5/PP2PPPP/R1BQKBNR w KQkq -",
      "rnbqkb1r/ppp2ppp/4pn2/3p2B1/2PP4/2N5/PP2PPPP/R2QKBNR b KQkq -",
      "rnbqk2r/ppp1bppp/4pn2/3p2B1/2PP4/2N5/PP2PPPP/R2QKBNR w KQkq -",
      "rnbqk2r/ppp1bppp/4pn2/3p2B1/2PP4/2N2N2/PP2PPPP/R2QKB1R b KQkq -",
      "rnbq1rk1/ppp1bppp/4pn2/3p2B1/2PP4/2N2N2/PP2PPPP/R2QKB1R w KQ -",
      "rnbq1rk1/ppp1bppp/4pn2/2Pp2B1/3P4/2N2N2/PP2PPPP/R2QKB1R b KQ -",
      "rnbq1rk1/p1p1bppp/1p2pn2/2Pp2B1/3P4/2N2N2/PP2PPPP/R2QKB1R w KQ -",
      "rnbq1rk1/p1p1bppp/1p2pn2/2Pp2B1/1P1P4/2N2N2/P3PPPP/R2QKB1R b KQ b3",
      "rnbq1rk1/p1p1bppp/4pn2/2pp2B1/1P1P4/2N2N2/P3PPPP/R2QKB1R w KQ -",
      "rnbq1rk1/p1p1bppp/4pn2/2Pp2B1/1P6/2N2N2/P3PPPP/R2QKB1R b KQ -",
      "rnbq1rk1/2p1bppp/4pn2/p1Pp2B1/1P6/2N2N2/P3PPPP/R2QKB1R w KQ a6",
      "rnbq1rk1/2p1bppp/4pn2/p1Pp2B1/1P6/P1N2N2/4PPPP/R2QKB1R b KQ -",
      "rnbq1rk1/2p1bppp/4pn2/p1P3B1/1P1p4/P1N2N2/4PPPP/R2QKB1R w KQ -",
      "rnbq1rk1/2p1bppp/4pB2/p1P5/1P1p4/P1N2N2/4PPPP/R2QKB1R b KQ -",
      "rnbq1rk1/2p1bp1p/4pp2/p1P5/1P1p4/P1N2N2/4PPPP/R2QKB1R w KQ -",
      "rnbq1rk1/2p1bp1p/4pp2/p1P5/NP1p4/P4N2/4PPPP/R2QKB1R b KQ -",
      "rnbq1rk1/2p1bp1p/5p2/p1P1p3/NP1p4/P4N2/4PPPP/R2QKB1R w KQ -",
      "rnbq1rk1/2p1bp1p/5p2/pPP1p3/N2p4/P4N2/4PPPP/R2QKB1R b KQ -",
      "rn1q1rk1/2p1bp1p/4bp2/pPP1p3/N2p4/P4N2/4PPPP/R2QKB1R w KQ -",
      "rn1q1rk1/2p1bp1p/4bp2/pPP1p3/N2p4/P4NP1/4PP1P/R2QKB1R b KQ -",
      "rn1q1rk1/4bp1p/2p1bp2/pPP1p3/N2p4/P4NP1/4PP1P/R2QKB1R w KQ -",
      "rn1q1rk1/4bp1p/2P1bp2/p1P1p3/N2p4/P4NP1/4PP1P/R2QKB1R b KQ -",
      "r2q1rk1/4bp1p/2n1bp2/p1P1p3/N2p4/P4NP1/4PP1P/R2QKB1R w KQ -",
      "r2q1rk1/4bp1p/2n1bp2/p1P1p3/N2p4/P4NP1/4PPBP/R2QK2R b KQ -",
      "1r1q1rk1/4bp1p/2n1bp2/p1P1p3/N2p4/P4NP1/4PPBP/R2QK2R w KQ -",
      "1r1q1rk1/4bp1p/2n1bp2/p1P1p3/N2p4/P4NP1/4PPBP/R1Q1K2R b KQ -",
      "1r1q1rk1/4bp1p/2n1bp2/p1P1p3/N7/P2p1NP1/4PPBP/R1Q1K2R w KQ -",
      "1r1q1rk1/4bp1p/2n1bp2/p1P1p3/N7/P2pPNP1/5PBP/R1Q1K2R b KQ -",
      "1r1q1rk1/4bp1p/2n1bp2/p1P5/N3p3/P2pPNP1/5PBP/R1Q1K2R w KQ -",
      "1r1q1rk1/4bp1p/2n1bp2/p1P5/N3p3/P2pP1P1/3N1PBP/R1Q1K2R b KQ -",
      "1r1q1rk1/4bp1p/2n1b3/p1P2p2/N3p3/P2pP1P1/3N1PBP/R1Q1K2R w KQ -",
      "1r1q1rk1/4bp1p/2n1b3/p1P2p2/N3p3/P2pP1P1/3N1PBP/R1Q2RK1 b - -",
      "1r1qr1k1/4bp1p/2n1b3/p1P2p2/N3p3/P2pP1P1/3N1PBP/R1Q2RK1 w - -",
      "1r1qr1k1/4bp1p/2n1b3/p1P2p2/N3p3/P2pPPP1/3N2BP/R1Q2RK1 b - -",
      "1r1qr1k1/4bp1p/4b3/p1P2p2/N2np3/P2pPPP1/3N2BP/R1Q2RK1 w - -",
      "1r1qr1k1/4bp1p/4b3/p1P2p2/N2Pp3/P2p1PP1/3N2BP/R1Q2RK1 b - -",
      "1r2r1k1/4bp1p/4b3/p1P2p2/N2qp3/P2p1PP1/3N2BP/R1Q2RK1 w - -",
      "1r2r1k1/4bp1p/4b3/p1P2p2/N2qp3/P2p1PP1/3N2BP/R1Q2R1K b - -",
      "1r2r1k1/4bp1p/4b3/p1P2p2/N2q4/P2ppPP1/3N2BP/R1Q2R1K w - -",
      "1r2r1k1/4bp1p/4b3/p1P2p2/q3p3/P2p1PP1/3N2BP/R1Q2R1K w - -",
      "1r2r1k1/4bp1p/4b3/p1P2p2/3q4/P1NppPP1/3N2BP/R1Q2R1K b - -",
      "1r2r1k1/5p1p/4bb2/p1P2p2/3q4/P1NppPP1/3N2BP/R1Q2R1K w - -",
      "1r2r1k1/5p1p/4bb2/p1P2p2/3q4/P1NppPP1/6BP/RNQ2R1K b - -",
      "1r2r1k1/5p1p/4bb2/p1P2p2/3q4/P1N1pPP1/3p2BP/RNQ2R1K w - -",
      "1r2r1k1/5p1p/4bb2/p1P2p2/3q4/P1N1pPP1/2Qp2BP/RN3R1K b - -",
      "1r2r1k1/5p1p/5b2/p1P2p2/3q4/PbN1pPP1/2Qp2BP/RN3R1K w - -",
      "1r2r1k1/5p1p/5b2/p1P2Q2/3q4/PbN1pPP1/3p2BP/RN3R1K b - -",
      "1r2r1k1/5p1p/5b2/p1P2Q2/3q4/PbN1pPP1/6BP/RN1q1R1K w - -",
      "1r2r1k1/5p1p/5b2/p1P2Q2/3q4/Pb2pPP1/6BP/RN1N1R1K b - -",
      "1r2r1k1/5p1p/5b2/p1P2Q2/3q4/P3pPP1/6BP/RN1b1R1K w - -",
      "1r2r1k1/5p1p/5b2/p1P2Q2/3q4/P1N1pPP1/6BP/R2b1R1K b - -",
      "1r2r1k1/5p1p/5b2/p1P2Q2/3q4/P1N2PP1/4p1BP/R2b1R1K w - -",
      "1r2r1k1/5p1p/5b2/p1P2Q2/3q4/P1N2PP1/4p1BP/3R1R1K b - -",
      "1r2r1k1/5p1p/5b2/p1P2Q2/8/P1q2PP1/4p1BP/3R1R1K w - -"
    ]
  }
}
```
