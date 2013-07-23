<?php

require_once('ParserStrStream.php');

/**
 * Parses condition strings
 * Wrote as an answer for this question (in German):
 * <http://www.tutorials.de/php/394774-suche-mit-beliebig-vielen-werten-und-argumenten-und-operatoren.html>
 */
class Parser {
	// Possible States
	/**
	 * Parsing attribute (including the operator)
	 */
	const ST_ATTR = 1;
	/**
	 * Parsing the value
	 */
	const ST_VAL  = 2;

	/**
	 * Possible operators
	 */
	private static $operators = array('>', '<', '=', '!=', '!');
	
	// The attributes below will be initialized in the init() function.
	/**
	 * Stores all operators' beginning characters (for improving performance).
	 */
	private static $opBeginnings;
	/**
	 * Stores the length of the longest operator.
	 */
	private static $maxOpLength;
	/**
	 * Will be set to true once init() was called.
	 */
	private static $isInit = false;
	
	public static function init() {
		self::$maxOpLength = max(array_map('mb_strlen', self::$operators));

		self::$opBeginnings = array_map(function ($val) {
			return mb_substr($val, 0, 1);
		}, self::$operators);
	}
	
	/**
	 * Parses the string and returns all conditions.
	 * @param string $str The input string
	 */
	public static function parse($str) {
		if (!self::$isInit) {
			self::init();
		}
		
		$stream = new ParserStrStream($str);
		
		$state = self::ST_ATTR;
		$curGroup = array();
		$groups = array();
		$inQuotes = false;
		$escape = false;
	
		do {
			$char = $stream->cur();
		
			if ($state == self::ST_ATTR) {
				if (in_array($char, self::$opBeginnings)) {
					$curGroup['op'] = '';
					
					// relative position from now on
					$relPos = 0;
					// the longest operator has the highest precedence
					$highestOp = '';
					$highestOpPos = 0;
					
					$subStream = ParserStrStream::createRefCopy($stream);
					do {
						$curGroup['op'] .= $subStream->cur();
						
						// possible operator found
						if (in_array($curGroup['op'], self::$operators)) {
							$state = self::ST_VAL;
							$highestOp = $curGroup['op'];
							$highestOpPos = $relPos;
						}
						$relPos++;
					} while ($relPos<self::$maxOpLength && $subStream->next() !== false);
					
					$curGroup['op'] = $highestOp;
					
					// if an operator matched
					if ($state == self::ST_VAL) {
						$stream->moveNext($highestOpPos);
					}
				}
				// current character is NOT a beginning of an operator
				else {
					if (!isset($curGroup['attr'])) {
						$curGroup['attr'] = '';
					}
					$curGroup['attr'] .= $char;
				}
			}
			
			else if ($state == self::ST_VAL) {
				if ($char == '\\') {
					$escape = !$escape;
				}
				
				else if ($char == '\'' && !$escape) {
					$inQuotes = !$inQuotes;
				}
				
				// end of current group
				else if ($char == '+' && !$inQuotes) {
					$groups[] = $curGroup;
					$curGroup = array();
					
					$state = self::ST_ATTR;
				}
				
				else {
					if (!isset($curGroup['val'])) {
						$curGroup['val'] = '';
					}
					$curGroup['val'] .= $char;
					$escape = false;
				}
			}
		} while ($stream->moveNext() !== false);
		
		if (!empty($curGroup)) {
			$groups[] = $curGroup;
		}
		
		return $groups;
	}
}