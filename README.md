# PHPdate class to bypass Y2K38 bug
This class is for bypass the 32-bit PHP Y2K38 bug

Everybody know about Y2K38 (Year 2038) on 32-bit PHP, so i decide to make this.

This class will use `$_SERVER["REQUEST_TIME_FLOAT"]` that formated as float (it's definitely infinite) to represent the time right now, so there's no Y2K38 bug, but we need PHP version 5.4 or higher for this.

If you don't need today date, you can create a date from string then call `timestamp()` function, this method will not use `$_SERVER["REQUEST_TIME_FLOAT"]` at all.

### Requirement
* PHP version >= 5.4
	
### Changelog
* **1.0 (16 Dec 2016):**
	- Initial release
* **1.1 (18 Dec 2016):**
	- Added "F" for a full textual representation of a month (January through December)
	- Added "M" for a short textual representation of a month, three letters (Jan through Dec)
	- Added "z" for the day of the year (starting from 0)
	- Added "l" for a full textual representation of the day of the week (Monday through Sunday)
	- Added "D" for a textual representation of a day, three letters (Mon through Sun)
	- Added "w" for numeric representation of the day of the week (0 for Sunday through 6 for Saturday)
	- Added "S" for english ordinal suffix for the day of the month, 2 characters (st, nd, rd or th)
	- Added "N" for ISO-8601 numeric representation of the day of the week (1 for Monday through 7 for Sunday)
* **1.2 (26 Feb 2017):**
	- Removed W (ISO-8601 week number of year) due to instability
	- Fix incorrect day name
	- Fix examples
* **1.3 (21 Sep 2026):**
	- Fix some typos
	
### Usage
```php
$your_variable = new PHPdate();			// Using PHP default timezone (you can set this in 'php.ini')
$your_variable = new PHPdate($GMT);		// Set your timezone
```

> **Note:** $GMT means GMT hours in your country, your timezone. Example: Asia/Jakarta is GMT +7

### Example:
```php
$date0 = new PHPdate();					// The default is your PHP default timezone
$date1 = new PHPdate(0);				// GMT/UTC
$date2 = new PHPdate("Asia/Jakarta");			// GMT +7 [Asia/Jakarta]
$date3 = new PHPdate(-7);				// GMT -7
```
	
### Implementation
```php
$date = new PHPdate(0);						// Create Object and date_timezone_set to GMT (+0 hour)
$my_timestamp = $date->timestamp("2016-12-16");			// Create timestamp of "2016-12-16 00:00:00"
$today_date = $date->format("Y-m-d H:i:s");			// Get today date and format it to "Y-m-d H:i:s"
$my_date = $date->format("Y-m-d H:i:s", $my_timestamp);		// Create date from timestamp
```
	
### timestamp() function only support these input formats
| Separator 1   | Separator 2   | Separator 3   |
| ------------- | ------------- | ------------- |
| `Y-m-d H:i:s` | `Y/m/d H:i:s` | `Y.m.d H:i:s` |
| `d-m-Y H:i:s` | `d/m/Y H:i:s` | `d.m.Y H:i:s` |
| `Y-m-d`       | `Y/m/d`       | `Y.m.d`       |
| `d-m-Y`       | `d/m/Y`       | `d.m.Y`       |

> **Note:** to add/change the format ability go to `timestamp()` function on line 491
	
### format() function only support these output formats
| Code | Output                                                                                     |
|:----:| -------------------------------------------------------------------------------------------|
|`Y`   | A full numeric representation of a year, 4 digits                                          |
|`y`   | A two digit representation of a year                                                       |
|`m`   | Numeric representation of a month, with leading zeros                                      |
|`n`   | Numeric representation of a month, without leading zeros                                   |
|`F`   | A full textual representation of a month (January through December)                        |
|`M`   | A short textual representation of a month, three letters (Jan through Dec)                 |
|`d`   | Day of the month, 2 digits with leading zeros                                              |
|`l`   | A full textual representation of the day of the week (Monday through Sunday)               |
|`D`   | A textual representation of a day, three letters (Mon through Sun)                         |
|`w`   | Numeric representation of the day of the week (0 for Sunday through 6 for Saturday)        |
|`N`   | ISO-8601 numeric representation of the day of the week (1 for Monday through 7 for Sunday) |
|`z`   | The day of the year (starting from 0 to 365/366)                                           |
|`S`   | English ordinal suffix for the day of the month, 2 characters (st, nd, rd or th)           |
|`j`   | Day of the month without leading zeros                                                     |
|`h`   | 12-hour format of an hour with leading zeros                                               |
|`H`   | 24-hour format of an hour with leading zeros                                               |
|`G`   | 24-hour format of an hour without leading zeros                                            |
|`g`   | 12-hour format of an hour without leading zeros                                            |
|`i`   | Minutes with leading zeros                                                                 |
|`s`   | Seconds, with leading zeros                                                                |
|`A`   | Uppercase Ante meridiem and Post meridiem                                                  |
|`a`   | Lowercase Ante meridiem and Post meridiem                                                  |

> **Note:** to add/change the format ability go to `format()` function on line 258
