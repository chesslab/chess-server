# /annotations_game

Annotated chess games.

---

### Usage

#### Example

```js
ws.send('/annotations_game');
```

```text
{
  "/annotations_game": [
    {
      "Event": "Steinitz - Zukertort World Championship Match",
      "Round": "19",
      "Site": "New Orleans, LA USA",
      "Date": "1886",
      "White": "Johannes Zukertort",
      "Black": "Wilhelm Steinitz",
      "WhiteElo": "?",
      "BlackElo": "?",
      "Result": "0-1",
      "ECO": "D50",
      "movetext": "{ Adapted notes, originally by Robert James Fischer from a television interview. } 1.d4 d5 2.c4 e6 3.Nc3 Nf6 4.Bg5 Be7 5.Nf3 O-O 6.c5 { is a mistake already; instead it should be played e3, naturally. } 6...b6 7.b4 bxc5 8.dxc5 a5 9.a3 d4 { is a fantastic move; it's the winning move. The pawn can't be taken with the knight because of axb4. } 10.Bxf6 gxf6 11.Na4 e5 { because the center is easily winning. Black's kingside weakness is nothing. } 12.b5 Be6 { with the idea of dominating the game with a powerful mobile center. } 13.g3 c6 14.bxc6 Nxc6 15.Bg2 Rb8 { threatening Bb3. } 16.Qc1 d3 17.e3 e4 18.Nd2 f5 19.O-O Re8 { is a very modern move; a quiet positional move. The rook is doing nothing now, but later... } 20.f3 { to break up the center, it's the only chance for White. } 20...Nd4 21.exd4 Qxd4+ 22.Kh1 e3 (22... Qxa4 { allows Black to easily regain material. }) 23.Nc3 Bf6 24.Ndb1 d2 25.Qc2 Bb3 26.Qxf5 d1=Q 27.Nxd1 Bxd1 28.Nc3 e2 29.Raxd1 Qxc3 { and White resigns. The center has prevailed. } 0-1"
    },
    ...
    {
      "Event": "Esbjerg",
      "Round": "9",
      "Site": "Esbjerg DEN",
      "Date": "1981.07.??",
      "White": "Raymond Keene",
      "Black": "Andrew Jonathan Mestel",
      "WhiteElo": "?",
      "BlackElo": "?",
      "Result": "1-0",
      "ECO": "A56",
      "movetext": "{ Adapted notes, originally by Raymond Keene. Mestel had also used this defence against Jakobsen in an earlier round of this tournament and it is a form of Benoni well suited to his uncompromising style. } 1.d4 Nf6 2.c4 c5 3.d5 e5 { Black offers a kind of \"space gambit\", but in return he gets long-term prospects of breakthroughs with b7-b5 and f7-f5. } 4.Nc3 d6 5.e4 g6 {In the magazine Modern Chess Theory I once wrote that with Nimzowitsch, Petrosian and Mestel the move g7-g6 (or g2-g3) does not necessarily portend a fianchetto of a bishop! Here, it seems, his idea was essentially to make g7 available for his king's knight.} 6.Be2 Nbd7 7.Nf3 (7.Bg5 { comes strongly into consideration here. } Be7 (7...h6 8.Be3 { after which Black will experience a slight weakness in his king's field if he tries to steer for...f7-f5 in the future. }) 8.Bh6 Bf8 9.Qd2) 7...Nh5 {The manoeuvre already known from Mestel's game with Jakobsen. } (8.g3 { would stop Nf4 but may look like wasting time.  There is nothing wrong with this move but at the time it struck me as needlessly passive. } Be7 { followed by Ng7 and later f7-f5. }) 8.O-O Nf4 {Black accepts the challenge.} 9.Bxf4 { although this weakens the dark squares in the centre (especially e5) it is the only consistent course. If White permits Nxe2 he has nothing. } exf4 10.Qd2 g5 { is an interesting move, and probably the best. Black keeps a firm grip on f4, and also conjures up some threats of g5-g4 (kicking White's knight) and then f4-f3. } (10...Qf6 11.Nb5 Kd8 { is somewhat awkward though perhaps playable. }) (10...Bh6 11.Nb5 Nf6 12.Nxd6+ Qxd6 13.e5 Qd8 (13...Ne4 14.exd6 (14.Qd3 $4 Qd8 15.Qxe4 Bf5 $19)) 14.exf6 Qxf6 15.b4 $1 { causes Black severe headaches. }) 11.e5 $1 { is a forced sacrifice before Black takes over the whole board with Bg7. The a1-h8 diagonal must be blocked. Nimzowitsch wrote something about this kind of vacuum-filling operation, which also frees squares behind (e4) for the officers. He explained that it was very similar to the \"passed pawn's lust to expand\"; for which see White's next move.} (11.g3 { is also possible, Mestel said afterwards. }) 11...dxe5 (11...Nxe5 12.Nxe5 dxe5 13.Ne4 Bd6 14.b4) 12.d6 Rb8 { is a subtle move which takes away some of the force from Nb5 (or Nd5) and Nc7. } (12...Bg7 13.Nd5 O-O 14.Ne7+ Kh8 15.Nxg5) 13.h4 $1 { is a second pawn sacrifice to disjoint Black's pawn phalanx on the kingside. It is vitally important to clear up the question of whether Black will have g5-g4 available as a resource.} gxh4 { Not just grabbing a pawn - also hoping that h4-h3 will, in the later part of the game, embarrass White's king. } (13...g4 14.Ng5 h6 15.Nd5 $1 Bg7 (15...Bxd6 16.Ne4 Be7 17.Bxg4 { with a total blockade. }) 16.Nc7+ Kf8 17.Nge6+ fxe6 18.Nxe6+ { wins. }) 14.Rfe1 {Preparing combinations in the e-file, which Black now underestimates.} Qf6 15.Ne4 Qh6 $1 { Here Black can suffer a cataclysm. } (15...Qg6 $2 16.Nxe5 $1 Nxe5 17.Bh5 Qg7 (17...Qxh5 18.Nf6+) (17...Qh6 18.d7+ Bxd7 19.Nxc5 Bxc5 20.Rxe5+ Be7 21.Rae1 { with a massive attack. }) 18.Qxf4 { and watch out for Bh5, it occurs again. }) 16.Qd5 Bg7 17.Nxc5 h3 $2 { completely overlooking White's threats. } (17...O-O 18.Nxd7 Bxd7 19.Nxe5 Be6 20.Qe4 h3 21.Bf3 Kh8 22.Rad1 { I like White's position with the passed pawn plus massive centralisation, especially since any endings are automatically won for White by the simple plan of c5, b4, b5, c6 making two connected passed pawns. However, Black is not totally without counter-chances, since White's king is not perfectly safe. }) 18.Nxe5 $1 Nxe5 19.d7+ Bxd7 20.Nxd7 hxg2 (20...Nxd7 { allows White win any way he likes. } 21.Bh5+ Ne5 (21...Kd8 22.Rad1 Qc6 23.Bg4) 22.Rxe5+) 21.Bh5 $3 { Black had not seen this annihilating blow. He must now submit to a combination which wins his queen and leaves his king exposed. } (21.Kxg2 Qg5+ 22.Kf1 Rd8) 21...Qxh5 22.Rxe5+ Bxe5 23.Nf6+ {Here we see the same theme as in the note to 15...Qh6.} Bxf6 24.Qxh5 (24.Re1+ $2 Qe5 $1 25.Rxe5+ Bxe5 26.Qxe5+ { with two rooks for the queen. }) 24...O-O 25.Kxg2 Bg7 26.Rh1 h6 27.Rh4 Rbe8 28.Rxf4 Re5 29.Rf5 Re6 30.b3 b6 {Black's only chance is to reach a structure with pawns on a5 and b6 and with his bishop on c5, but it's not really possible. His defence was also hampered by desperate time-trouble.} 31.Rf3 Re5 32.Qh3 Rd8 33.Rg3 Kh8 34.Qg4 Rg5 35.Qf3 Rxg3+ 36.Kxg3 Be5+ 37.Kg2 Kg7 38.Qg4+ Kf8 39.Qh5 Bg7 40.c5 $1 Rc8 (40...bxc5 41.Qxc5+ { and Qxa7 }) 41.cxb6 axb6 42.Qb5 (42...Bd4 43.Qd7) (42...Rb8 43.a4 Bc3 44.b4 $1 { and a4-a5 } (44.a5 $2 Bxa5 45.b4 Bxb4 $1)) 1-0"
    }
  ]
}
```
