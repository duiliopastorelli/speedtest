# SpeedPerformance
Webpage speed test runner

# Install

```$ npm install```

###Optional values for WPT
- "email": "string"

# Develop

## Test suite

```$ npm test```

# Data
##WebPageTest
###Test Request Object
- statusCode - int (200)
- statusText - string (ok)
- data
    - testId - string
    - ownerKey - string
    - jsonUrl - string
    - xmlUrl - string
    - userUrl - string
    - summaryCSV - string
    - detailCSV - string