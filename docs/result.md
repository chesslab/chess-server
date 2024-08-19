# /result

Openings results.

---

## Usage

### Example

```js
ws.send('/result');
```

```text
{
  "/result": {
    "/result": {
      "drawRate": [
        {
          "ECO": "C42",
          "total": 286
        },
        ...
        {
          "ECO": "E06",
          "total": 100
        }
      ],
      "winRateForWhite": [
        {
          "ECO": "A45",
          "total": 218
        },
        ...
        {
          "ECO": "B33",
          "total": 104
        }
      ],
      "winRateForBlack": [
        {
          "ECO": "A07",
          "total": 129
        },
        ...
        {
          "ECO": "B10",
          "total": 103
        }
      ]
    }
  }
}
```
