# SpeedPerformance
Webpage speed test runner

# Install

```$ npm install```

###Optional values for WPT
- "email": "string"

# Develop

Run:

```$ DEBUG=myapp:* npm start```

And go to "http://127.0.0.1:3000/"

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