# /totp_signin

TOTP sign in.

## `username`

The username.

## `password`

The password.

---

### Usage

#### Example

```js
ws.send('/totp_signin "{\\"username\\":\\"graceful_elephant\\",\\"password\\":\\"513967906\\"}"');
```

```text
{
  "/totp_signin": {
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJhc3luYy5jaGVzc2xhYmxhYi5vcmciLCJpYXQiOjE3MjYxNjcwMDcsImV4cCI6MTcyNjE3MDYwNywidXNlcm5hbWUiOiJjb3VyYWdlb3VzX2FybWFkaWxsbyIsImVsbyI6MTQzMX0.X-IsxooRmsE6tiSZMX044k-vwonWc1AlCR5q9xUJiMs"
  }
}
```

Decoded JWT:

```text
{
  "iss": "async.chesslablab.org",
  "iat": 1726167007,
  "exp": 1726170607,
  "username": "graceful_elephant",
  "elo": 1431
}
```
