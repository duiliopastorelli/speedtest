# SpeedPerformance
Webpage speed test runner

# Install
```$ composer install```

##Configuration
SpeedPerformance needs a configuration file for work properly.

This file must be a JSON fileand must be stored in /config.json

Its basic structure looks like:

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

# Class usage
```php
SpeedPerformance\SpeedPerformance::runTests([$appStatus]);
```

$appStatus is optional and can have the following values:
- "dist" -> load the normal config.json file (same as empty)
- "test" -> load the mocks/testConfig.json file
- "testBadConfig" -> load the mocks/testConfigBad.json file

# Develop

## Test suite

For run the tests use the command

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