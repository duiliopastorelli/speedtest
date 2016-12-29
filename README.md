# SpeedPerformance
Webpage speed test runner

# Install
```$ composer install```

##Configuration
SpeedPerformance needs a configuration file for work properly.

This file must be a JSON file, could be stored everywhere and called with:

```
$wptConfigTest = new SpeedPerformance\Settings()
$wptConfigTest->getSettings('PATH');
```

Its basic structure looks like:

./config.json

```json
{
  "wpt":{
    "key": "YourKeyHere",
    "url":{
      "0": "https://example.com",
      "1": "https://example1.com"
    }
  }
}
```

###Optional values for WPT
- "email": "string"

# Develop

## Test suite

For run a test use the command

```vendor/bin/phpunit tests/[FILENAME]```

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