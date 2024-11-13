# /totp_refresh

Refresh the TOTP access token.

## `access_token`

The current access token.

---

### Usage

#### Example

```js
ws.send('/totp_refresh "{\\"access_token\\":\\"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJhc3luYy5jaGVzc2xhYmxhYi5vcmciLCJpYXQiOjE3MjYxNjcwMDcsImV4cCI6MTcyNjE3MDYwNywidXNlcm5hbWUiOiJjb3VyYWdlb3VzX2FybWFkaWxsbyIsImVsbyI6MTQzMX0.X-IsxooRmsE6tiSZMX044k-vwonWc1AlCR5q9xUJiMs\\"}"');
```

```text
{
  "/totp_refresh": {
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJhc3luYy5jaGVzc2xhYmxhYi5vcmciLCJpYXQiOjE3MjYxNjgwMTYsImV4cCI6MTcyNjE3MTYxNiwidXNlcm5hbWUiOiJjb3VyYWdlb3VzX2FybWFkaWxsbyIsImVsbyI6MTQxOH0.Cc4rX3XZ-5eRJ6gpK12XuealgM7ubmal8xRFTXoGxMw"
  }
}
```

Decoded JWT:

```text
{
  "iss": "async.chesslablab.org",
  "iat": 1726168016,
  "exp": 1726171616,
  "username": "graceful_elephant",
  "elo": 1418
}
```
