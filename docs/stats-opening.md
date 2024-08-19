# /stats_opening

Openings results.

---

## Usage

### Example

```js
ws.send('/stats_opening');
```

```text
{
  "/stats_opening": {
    "/stats_opening": {
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
