# /totp_signup

TOTP sign up URL.

---

### Usage

#### Example

```js
ws.send('/totp_signup');
```

```text
{
  "/totp_signup": {
    "uri": "https://api.qrserver.com/v1/create-qr-code/?data=otpauth%3A%2F%2Ftotp%2FChesslaBlab%253Alonging_rook%3Fdigits%3D9%26image%3Dhttps%253A%252F%252Fchesslablab.org%252Flogo.png%26issuer%3DChesslaBlab%26secret%3DIZ2COUCZFZMGYVRPKURGGSL3GVPTGZLNGZFCE3BZGAWWWZJHH5WUGMSNNJEC2QROKNTVQYZVK57HEKKQFZEDQZB7FUYCQPZ5L44GAXTPOFTWSILLJJHFAYD6N5ESK7LYKJIGUL3HIR2HW5DFMVGWGRTGFB3CUUBJJR3E4QRXJ45VYYJVONGVAOK6KUZTYUK6IAVXGOT4JF7C2&size=300x300&ecc=M"
  }
}
```
