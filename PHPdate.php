<?php
/*
	Author		: Muhammad Faid Alfarisi
	
	Version		: 1.3 (2026-09-21)
	
	Changelog	:
		----------------
		1.0 [2016-12-16]
		----------------
			- Initial release
		----------------
		1.1 [2016-12-18]
		----------------
			- Added "F" for a full textual representation of a month (January through December)
			- Added "M" for a short textual representation of a month, three letters (Jan through Dec)
			- Added "z" for the day of the year (starting from 0)
			- Added "l" for a full textual representation of the day of the week (Monday through Sunday)
			- Added "D" for a textual representation of a day, three letters (Mon through Sun)
			- Added "w" for numeric representation of the day of the week (0 for Sunday through 6 for Saturday)
			- Added "S" for english ordinal suffix for the day of the month, 2 characters (st, nd, rd or th)
			- Added "N" for ISO-8601 numeric representation of the day of the week (1 for Monday through 7 for Sunday)
		----------------
		1.2 [2017-02-26]
		----------------
			- Removed W (ISO-8601 week number of year) due to instability
			- Fix incorrect day name
			- Fix examples
		----------------
		1.3 [2026-09-21]
		----------------
			- Fix some typos
	
	Usage:
	------
		$your_variable = new PHPdate();			// Using PHP default timezone (you can set this in 'php.ini')
		$your_variable = new PHPdate($GMT);		// Set your timezone
		==========================================================================================
		Note: $GMT means GMT hours in your country, your timezone. Example: Asia/Jakarta is GMT +7
		==========================================================================================
		Example:
		--------
			$date0 = new PHPdate();					// The default is your PHP default timezone
			$date1 = new PHPdate(0);				// GMT/UTC
			$date2 = new PHPdate("Asia/Jakarta");	// GMT +7 [Asia/Jakarta]
			$date3 = new PHPdate(-7);				// GMT -7
	
	Implementation:
	---------------
		$date = new PHPdate(0);										// Create Object and date_timezone_set to GMT (+0 hour)
		$my_timestamp = $date->timestamp("2016-12-16");				// Create timestamp of "2016-12-16 00:00:00"
		$today_date = $date->format("Y-m-d H:i:s");					// Get today date and format it to "Y-m-d H:i:s"
		$my_date = $date->format("Y-m-d H:i:s", $my_timestamp);		// Create date from timestamp
	
	timestamp only support these format:
	------------------------------------
		=> Y-m-d H:i:s	||	Y/m/d H:i:s	||	Y.m.d H:i:s
		=> d-m-Y H:i:s	||	d/m/Y H:i:s	||	d.m.Y H:i:s
		=> Y-m-d		||	Y/m/d		||	Y.m.d
		=> d-m-Y		||	d/m/Y		||	d.m.Y
		==================================================================================
		Note: to add/change the format ability go to line timestamp() function on line 491
		==================================================================================
	
	format only support these output:
	-------------------------------------
		Y => A full numeric representation of a year, 4 digits
		y => A two digit representation of a year
		m => Numeric representation of a month, with leading zeros
		n => Numeric representation of a month, without leading zeros
		F => A full textual representation of a month (January through December)
		M => A short textual representation of a month, three letters (Jan through Dec)
		d => Day of the month, 2 digits with leading zeros
		l => A full textual representation of the day of the week (Monday through Sunday)
		D => A textual representation of a day, three letters (Mon through Sun)
		w => Numeric representation of the day of the week (0 for Sunday through 6 for Saturday)
		N => ISO-8601 numeric representation of the day of the week (1 for Monday through 7 for Sunday)
		z => The day of the year (starting from 0 to 365/366)
		Z => Timezone offset in seconds (-43200 through 50400)
		S => English ordinal suffix for the day of the month, 2 characters (st, nd, rd or th)
		j => Day of the month without leading zeros
		h => 12-hour format of an hour with leading zeros
		H => 24-hour format of an hour with leading zeros
		G => 24-hour format of an hour without leading zeros
		g => 12-hour format of an hour without leading zeros
		i => Minutes with leading zeros
		s => Seconds, with leading zeros
		A => Uppercase Ante meridiem and Post meridiem
		a => Lowercase Ante meridiem and Post meridiem
		===============================================================================
		Note: to add/change the format ability go to line format() function on line 258
		===============================================================================
*/


class PHPdate {
	private $GMT;
	private $DOM = array(31, 0, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
	private $NOM = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
	private $NOD = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
	private $TZN = array(
		"pacific/midway"		=> -11,		// GMT - 11:00
		"us/samoa"				=> -11,		// GMT - 11:00
		"us/hawaii"				=> -10,		// GMT - 10:00
		"us/alaska"				=> -9,		// GMT - 09:00
		"us/pacific"			=> -8,		// GMT - 08:00
		"america/tijuana"		=> -8,		// GMT - 08:00
		"us/arizona"			=> -7,		// GMT - 07:00
		"us/mountain"			=> -7,		// GMT - 07:00
		"america/chihuahua"		=> -7,		// GMT - 07:00
		"america/mazatlan"		=> -7,		// GMT - 07:00
		"america/mexico_city"	=> -6,		// GMT - 06:00
		"america/monterrey"		=> -6,		// GMT - 06:00
		"canada/saskatchewan"	=> -6,		// GMT - 06:00
		"us/central"			=> -6,		// GMT - 06:00
		"us/eastern"			=> -5,		// GMT - 05:00
		"us/east-indiana"		=> -5,		// GMT - 05:00
		"america/bogota"		=> -5,		// GMT - 05:00
		"america/lima"			=> -5,		// GMT - 05:00
		"america/caracas"		=> -4.5,	// GMT - 04:30
		"canada/atlantic"		=> -4,		// GMT - 04:00
		"america/la_paz"		=> -4,		// GMT - 04:00
		"america/santiago"		=> -4,		// GMT - 04:00
		"canada/newfoundland"	=> -3.5,	// GMT - 03:30
		"america/buenos_aires"	=> -3,		// GMT - 03:00
		"greenland"				=> -3,		// GMT - 03:00
		"atlantic/stanley"		=> -2,		// GMT - 02:00
		"atlantic/azores"		=> -1,		// GMT - 01:00
		"atlantic/cape_verde"	=> -1,		// GMT - 01:00
		"africa/casablanca"		=> 0,		// GMT
		"europe/dublin"			=> 0,		// GMT
		"europe/lisbon"			=> 0,		// GMT
		"utc"					=> 0,		// GMT
		"gmt"					=> 0,		// GMT
		"europe/london"			=> 0,		// GMT
		"africa/monrovia"		=> 0,		// GMT
		"europe/amsterdam"		=> 1,		// GMT + 01:00
		"europe/belgrade"		=> 1,		// GMT + 01:00
		"europe/berlin"			=> 1,		// GMT + 01:00
		"europe/bratislava"		=> 1,		// GMT + 01:00
		"europe/brussels"		=> 1,		// GMT + 01:00
		"europe/budapest"		=> 1,		// GMT + 01:00
		"europe/copenhagen"		=> 1,		// GMT + 01:00
		"europe/ljubljana"		=> 1,		// GMT + 01:00
		"europe/madrid"			=> 1,		// GMT + 01:00
		"europe/paris"			=> 1,		// GMT + 01:00
		"europe/prague"			=> 1,		// GMT + 01:00
		"europe/rome"			=> 1,		// GMT + 01:00
		"europe/sarajevo"		=> 1,		// GMT + 01:00
		"europe/skopje"			=> 1,		// GMT + 01:00
		"europe/stockholm"		=> 1,		// GMT + 01:00
		"europe/vienna"			=> 1,		// GMT + 01:00
		"europe/warsaw"			=> 1,		// GMT + 01:00
		"europe/zagreb"			=> 1,		// GMT + 01:00
		"europe/athens"			=> 2,		// GMT + 02:00
		"europe/bucharest"		=> 2,		// GMT + 02:00
		"africa/cairo"			=> 2,		// GMT + 02:00
		"africa/harare"			=> 2,		// GMT + 02:00
		"europe/helsinki"		=> 2,		// GMT + 02:00
		"europe/istanbul"		=> 2,		// GMT + 02:00
		"asia/jerusalem"		=> 2,		// GMT + 02:00
		"europe/kiev"			=> 2,		// GMT + 02:00
		"europe/minsk"			=> 2,		// GMT + 02:00
		"europe/riga"			=> 2,		// GMT + 02:00
		"europe/sofia"			=> 2,		// GMT + 02:00
		"europe/tallinn"		=> 2,		// GMT + 02:00
		"europe/vilnius"		=> 2,		// GMT + 02:00
		"asia/baghdad"			=> 3,		// GMT + 03:00
		"asia/kuwait"			=> 3,		// GMT + 03:00
		"africa/nairobi"		=> 3,		// GMT + 03:00
		"asia/riyadh"			=> 3,		// GMT + 03:00
		"europe/moscow"			=> 3,		// GMT + 03:00
		"asia/tehran"			=> 3.5,		// GMT + 03:30
		"asia/baku"				=> 4,		// GMT + 04:00
		"europe/volgograd"		=> 4,		// GMT + 04:00
		"asia/muscat"			=> 4,		// GMT + 04:00
		"asia/tbilisi"			=> 4,		// GMT + 04:00
		"asia/yerevan"			=> 4,		// GMT + 04:00
		"asia/kabul"			=> 4.5,		// GMT + 04:30
		"asia/karachi"			=> 5,		// GMT + 05:00
		"asia/tashkent"			=> 5,		// GMT + 05:00
		"asia/kolkata"			=> 5.5,		// GMT + 05:30
		"asia/kathmandu"		=> 5.75,	// GMT + 05:45
		"asia/yekaterinburg"	=> 6,		// GMT + 06:00
		"asia/almaty"			=> 6,		// GMT + 06:00
		"asia/dhaka"			=> 6,		// GMT + 06:00
		"asia/novosibirsk"		=> 7,		// GMT + 07:00
		"asia/bangkok"			=> 7,		// GMT + 07:00
		"asia/jakarta"			=> 7,		// GMT + 07:00
		"asia/krasnoyarsk"		=> 8,		// GMT + 08:00
		"asia/chongqing"		=> 8,		// GMT + 08:00
		"asia/hong_kong"		=> 8,		// GMT + 08:00
		"asia/kuala_lumpur"		=> 8,		// GMT + 08:00
		"australia/perth"		=> 8,		// GMT + 08:00
		"asia/singapore"		=> 8,		// GMT + 08:00
		"asia/taipei"			=> 8,		// GMT + 08:00
		"asia/ulaanbaatar"		=> 8,		// GMT + 08:00
		"asia/urumqi"			=> 8,		// GMT + 08:00
		"asia/irkutsk"			=> 9,		// GMT + 09:00
		"asia/seoul"			=> 9,		// GMT + 09:00
		"asia/tokyo"			=> 9,		// GMT + 09:00
		"australia/adelaide"	=> 9.5,		// GMT + 09:30
		"australia/darwin"		=> 9.5,		// GMT + 09:30
		"asia/yakutsk"			=> 10,		// GMT + 10:00
		"australia/brisbane"	=> 10,		// GMT + 10:00
		"australia/canberra"	=> 10,		// GMT + 10:00
		"pacific/guam"			=> 10,		// GMT + 10:00
		"australia/hobart"		=> 10,		// GMT + 10:00
		"australia/melbourne"	=> 10,		// GMT + 10:00
		"pacific/port_moresby"	=> 10,		// GMT + 10:00
		"australia/sydney"		=> 10,		// GMT + 10:00
		"asia/vladivostok"		=> 11,		// GMT + 11:00
		"asia/magadan"			=> 12,		// GMT + 12:00
		"pacific/auckland"		=> 12,		// GMT + 12:00
		"pacific/fiji"			=> 12		// GMT + 12:00
	);
	
	// Set GMT on initialize
	public function PHPdate($gmt = "") {
		if($gmt === "") {
			$gmt = date_default_timezone_get();
		}
		if(is_numeric($gmt)) {
			$this->GMT = $gmt;
		} else {
			$gmt = strtolower($gmt);
			$gmt = str_replace(" ", "", $gmt);
			if(isset($this->TZN[$gmt])) {
				$this->GMT = $this->TZN[$gmt];
			} else {
				$gmt = date_default_timezone_get();
				$gmt = strtolower($gmt);
				$gmt = str_replace(" ", "", $gmt);
				$this->GMT = isset($this->TZN[$gmt]) ? $this->TZN[$gmt] : 0;
			}
		}
	}
	
	// Set GMT for user
	public function setGMT($gmt) {
		if(is_numeric($gmt)) {
			$this->GMT = $gmt;
		} else {
			$gmt = strtolower($gmt);
			$gmt = str_replace(" ", "", $gmt);
			if(isset($this->TZN[$gmt])) {
				$this->GMT = $this->TZN[$gmt];
			} else {
				$gmt = date_default_timezone_get();
				$gmt = strtolower($gmt);
				$gmt = str_replace(" ", "", $gmt);
				$this->GMT = isset($this->TZN[$gmt]) ? $this->TZN[$gmt] : 0;
			}
		}
	}
	
	// format
	public function format($date_format, $timestamp="") {
		$fmt = str_split($date_format, 1);
		$minus = false;
		$timestamp = $timestamp === "" ? $this->timestamp() : floatval($timestamp);
		if($timestamp < 0) {
			$timestamp = floatval($timestamp * -1);
			$minus = true;
		}
		$timestamp = floatval($timestamp + ($this->GMT * 3600));	// Adjust timestamp with your timezone
		$day_name = $minus ? 3 : 4;									// December, 31st 1969 is Wednesday and January, 1st 1970 is Thursday
		
		// --- YEAR --- //
		$add = 31536000;
		$y = $minus ? 1969 : 1970;
		$tmp = 0;
		while($timestamp >= ($tmp + $add)) {
			$tmp += $add;
			if($minus) {
				$day_name -= $this->isLeap($y) ? 2 : 1;
				$day_name = $day_name < 0 ? $day_name + 7 : $day_name;
				$y--;
			} else {
				$day_name += $this->isLeap($y) ? 2 : 1;
				$day_name = $day_name > 6 ? $day_name - 7 : $day_name;
				$y++;
			}
			$add = $this->isLeap($y) ? 31622400 : 31536000;
		}
		// --- YEAR --- //
		
		$timestamp -= $tmp;
		if($minus && $timestamp <= 0) {
			$day_name++;
			$day_name = $day_name > 6 ? 0 : $day_name;
			$y++;
			$timestamp = 0;
			$minus = false;
		}
		
		$doy = $this->isLeap($y) ? 365 : 364;									// Day of year Initiaize
		$doy = $minus ? $doy : 0;												// Day of year (check if year below 1970)
		
		// --- MONTH --- //
		$add = 2678400;
		$m = $minus ? 12 : 1;
		$tmp = 0;
		while($timestamp >= ($tmp + $add)) {
			$tmp += $add;
			if($m == 2) {
				if($this->isLeap($y)) {
					$day_add = 29;
				} else {
					$day_add = 28;
				}
			} else {
				$day_add = $this->DOM[($m - 1)];
			}
			if($minus) {
				$m--;
			} else {
				$m++;
			}
			if($m == 2) {
				if($this->isLeap($y)) {
					$add = 29 * 86400;
				} else {
					$add = 28 * 86400;
				}
			} else {
				$add = $this->DOM[($m - 1)] * 86400;
			}
			if($minus) {
				$doy -= $day_add;
				$day_name -= $day_add - 28;
				$day_name = $day_name < 0 ? $day_name + 7 : $day_name;
			} else {
				$doy += $day_add;
				$day_name += $day_add - 28;
				$day_name = $day_name > 6 ? $day_name - 7 : $day_name;
			}
		}
		// --- MONTH --- //
		
		$timestamp -= $tmp;
		if($minus && $timestamp <= 0) {
			$m++;
			$day_name++;
			$day_name = $day_name > 6 ? 0 : $day_name;
			$timestamp = 0;
			$minus = false;
		}
		
		// --- DAY --- //
		if($m == 2) {
			if($this->isLeap($y)) {
				$day_in_month = 29;
			} else {
				$day_in_month = 28;
			}
		} else {
			$day_in_month = $this->DOM[($m - 1)];
		}
		$add = 86400;
		$d = $minus ? $day_in_month : 1;
		$tmp = 0;
		while($timestamp >= ($tmp + $add)) {
			$tmp += $add;
			if($minus) {
				$d--;
				$doy--;
				$day_name--;
				$day_name = $day_name < 0 ? 6 : $day_name;
			} else {
				$d++;
				$doy++;
				$day_name++;
				$day_name = $day_name > 6 ? 0 : $day_name;
			}
		}
		// --- DAY --- //
		
		$timestamp -= $tmp;
		if($minus && $timestamp <= 0) {
			$d++;
			$doy++;
			$day_name++;
			$day_name = $day_name > 6 ? 0 : $day_name;
			$timestamp = 0;
			$minus = false;
		}
		
		// --- HOUR --- //
		$add = 3600;
		$h = $minus ? 23 : 0;
		$tmp = 0;
		while($timestamp >= ($tmp + $add)) {
			$tmp += $add;
			if($minus) {
				$h--;
			} else {
				$h++;
			}
		}
		// --- HOUR --- //
		
		$timestamp -= $tmp;
		if($minus && $timestamp <= 0) {
			$h++;
			$timestamp = 0;
			$minus = false;
		}
		
		// --- MINUTE --- //
		$add = 60;
		$i = $minus ? 59 : 0;
		$tmp = 0;
		while($timestamp >= ($tmp + $add)) {
			$tmp += $add;
			if($minus) {
				$i--;
			} else {
				$i++;
			}
		}
		// --- MINUTE --- //
		
		$timestamp -= $tmp;
		if($minus && $timestamp <= 0) {
			$i++;
			$timestamp = 0;
			$minus = false;
		}
		
		// --- SECOND --- //
		$s = $minus ? 60 - $timestamp : $timestamp;
		// --- SECOND --- //
		
		// --- 12-HOUR FORMAT --- //
		$h12 = $h > 12 ? $h - 12 : $h;
		// --- 12-HOUR FORMAT --- //
		
		// --- LEADING ZEROS --- //
		$m = $m < 10 ? "0{$m}" : $m;
		$d = $d < 10 ? "0{$d}" : $d;
		$h = $h < 10 ? "0{$h}" : $h;
		$i = $i < 10 ? "0{$i}" : $i;
		$s = $s < 10 ? "0{$s}" : $s;
		// --- LEADING ZEROS --- //
		
		$replacement = array(
			"Y" => $y,											// A full numeric representation of a year, 4 digits
			"y" => substr("{$y}", -2),							// A two digit representation of a year
			"m" => $m,											// Numeric representation of a month, with leading zeros
			"n" => intval($m),									// Numeric representation of a month, without leading zeros
			"F" => $this->NOM[(intval($m) - 1)],				// A full textual representation of a month (January through December)
			"M" => substr($this->NOM[(intval($m) - 1)], 0, 3),	// A short textual representation of a month, three letters (Jan through Dec)
			"d" => $d,											// Day of the month, 2 digits with leading zeros
			"l" => $this->NOD[$day_name],						// A full textual representation of the day of the week (Monday through Sunday)
			"D" => substr($this->NOD[$day_name], 0, 3),			// A textual representation of a day, three letters (Mon through Sun)
			"w" => $day_name,									// Numeric representation of the day of the week (0 for Sunday through 6 for Saturday)
			"N" => $day_name == 0 ? 7 : $day_name,				// ISO-8601 numeric representation of the day of the week (1 for Monday through 7 for Sunday)
			"z" => $doy,										// The day of the year (starting from 0 to 365/366)
			"Z" => $this->GMT * 3600,							// Timezone offset in seconds (-43200 through 50400)
			"S" => $this->getSuffix($d),						// English ordinal suffix for the day of the month, 2 characters (st, nd, rd or th)
			"j" => intval($d),									// Day of the month without leading zeros
			"h" => $h12 < 10 ? "0{$h12}" : $h12,				// 12-hour format of an hour with leading zeros
			"H" => $h,											// 24-hour format of an hour with leading zeros
			"G" => intval($h),									// 24-hour format of an hour without leading zeros
			"g" => $h12,										// 12-hour format of an hour without leading zeros
			"i" => $i,											// Minutes with leading zeros
			"s" => $s,											// Seconds, with leading zeros
			"A" => intval($h) > 12 ? "PM" : "AM",				// Uppercase Ante meridiem and Post meridiem
			"a" => intval($h) > 12 ? "pm" : "am"				// Lowercase Ante meridiem and Post meridiem
		);
		
		$result = "";
		foreach($fmt as $str) {
			$result .= isset($replacement[$str]) ? $replacement[$str] : $str;
		}
		return $result;
	}
	
	// Function to check Leap Year
	private function isLeap($year) {
		return ((($year % 4) == 0) && ((($year % 100) != 0) || (($year % 400) == 0)));
	}
	
	// Function to get the english day suffix (st, nd, rd and th)
	private function getSuffix($number) {
		$orinum = intval($number);
		$number = substr($number, -1);
		$number = intval($number);
		$suffix = "th";
		if($orinum < 10 || $orinum > 19) {
			if($number == 1) {
				$suffix = "st";
			} else if($number == 2) {
				$suffix = "nd";
			} else if($number == 3) {
				$suffix = "rd";
			}
		}
		return $suffix;
	}
	
	/*
		Function to get the timestamp
		Only support:
			- "Y-m-d H:i:s"
			- "Y-m-d"
			- "d-m-Y H:i:s"
			- "d-m-Y"
			- "Y/m/d H:i:s"
			- "Y/m/d"
			- "d/m/Y H:i:s"
			- "d/m/Y"
			- "Y.m.d H:i:s"
			- "Y.m.d"
			- "d.m.Y H:i:s"
			- "d.m.Y"
	*/
	public function timestamp($string = "") {
		if($string === "") {
			return floor(floatval($_SERVER["REQUEST_TIME_FLOAT"]));
		} else {
			$string = explode(" ", $string);
			
			// Replace any "/" or "." separator to "-"
			$string[0] = str_replace(array("/", "."), array("-", "-"), $string[0]);
			
			$date = explode("-", $string[0]);
			
			// Check the order (d-m-Y or Y-m-d) and fix it (Year should bigger than day, that's why i'm not support 2-digits year)!!!
			if(intval($date[0]) < intval($date[2])) {
				$day = $date[0];
				$date[0] = $date[2];
				$date[2] = $day;
			}
			
			// Check if clock included (Use 1 space to separate date and time, don't give any space if time not included)!!!
			if(isset($string[1])) {
				$time = explode(":", $string[1]);
			} else {
				$time = array("00", "00", "00");
			}
			
			// Check the year and call a function
			if(intval($date[0]) < 1970) {
				return $this->timestampNegative($date, $time);	// Call get negative timestamp if year <  1970
			} else {
				return $this->timestampPositive($date, $time);	// Call get positive timestamp if year >= 1970
			}
		}
	}
	
	// Function to get microsecond from date string
	public function getMicrosecond($string = "") {
		return floatval($this->timestamp($string) * 1000);
	}
	
	// Called when year >= 1970
	private function timestampPositive($date, $time) {
		$leap = 0;
		$not_leap = 0;
		
		$yr = intval($date[0]);
		$mt = intval($date[1]) - 1;
		$dy = intval($date[2]) - 1;
		
		// --- YEAR --- //
		for($i = 1970; $i < $yr; $i++) {
			if($this->isLeap($i)) {
				$leap++;
			} else {
				$not_leap++;
			}
		}
		$y = floatval(floatval($not_leap * 31536000) + floatval($leap * 31622400));
		// --- YEAR --- //
		
		// --- MONTH --- //
		$m = 0;
		for($i = 0; $i < $mt; $i++) {
			if($i === 1) {
				if($this->isLeap($yr)) {
					$m += 29 * 86400;
				} else {
					$m += 28 * 86400;
				}
			} else {
				$m += $this->DOM[$i] * 86400;
			}
		}
		// --- MONTH --- //
		
		// --- DAY, HOUR, MINUTE, SECOND --- //
		$d = floatval($dy * 86400);
		$h = floatval(intval($time[0]) * 3600);
		$i = floatval(intval($time[1]) * 60);
		$s = floatval($time[2]);
		// --- DAY, HOUR, MINUTE, SECOND --- //
		
		return floatval(($y + $m + $d + $h + $i + $s) - ($this->GMT * 3600));
	}
	
	// Called when year < 1970
	private function timestampNegative($date, $time) {
		$leap = 0;
		$not_leap = 0;
		
		$yr = intval($date[0]);
		$mt = intval($date[1]) - 1;
		$dy = intval($date[2]);
		
		// --- YEAR --- //
		for($i = 1969; $i > $yr; $i--) {
			if($this->isLeap($i)) {
				$leap++;
			} else {
				$not_leap++;
			}
		}
		$y = floatval(floatval($not_leap * 31536000) + floatval($leap * 31622400));
		// --- YEAR --- //
		
		// --- MONTH --- //
		$m = 0;
		for($i = 11; $i > $mt; $i--) {
			if($i === 1) {
				if($this->isLeap($yr)) {
					$m += 29 * 86400;
				} else {
					$m += 28 * 86400;
				}
			} else {
				$m += $this->DOM[$i] * 86400;
			}
		}
		// --- MONTH --- //
		
		// --- DAY --- //
		if($mt === 1) {
			if($this->isLeap($yr)) {
				$day_in_month = 29;
			} else {
				$day_in_month = 28;
			}
		} else {
			$day_in_month = $this->DOM[$mt];
		}
		$d = floatval(($day_in_month - $dy) * 86400);
		// --- DAY --- //
		
		// --- DAY, HOUR, MINUTE, SECOND --- //
		$h = floatval((23 - intval($time[0])) * 3600);
		$i = floatval((59 - intval($time[1])) * 60);
		$s = floatval(60 - intval($time[2]));
		// --- DAY, HOUR, MINUTE, SECOND --- //
		
		return floatval(0 - floatval(($y + $m + $d + $h + $i + $s) - ($this->GMT * 3600)));
	}
}
