<?php
/**
 * A string stream, especially useful for parsers.
 */
class ParserStrStream {
	private $chars = array();
	private $len = 0;
	private $pos = 0;
	
	public function __construct($str) {
		if (!empty($str)) {
			$this->chars = preg_split('/(?<!^)(?!$)/u', $str);
			$this->len = count($this->chars);
		}
	}
	
	/**
	 * Returns the character array as a reference.
	 */
	public function &getCharsAsRef() {
		return $this->chars;
	}

	/**
	 * References the current character's array from another stream object.
	 */
	public function setAsRefObj(ParserStrStream &$old) {
		$this->chars = $old->getCharsAsRef();
		$this->len = $old->len();
		$this->pos = $old->pos();
	}
	
	/**
	 * Helper function for creating an independent copy which only references the original character's array (for performance reasons).
	 */
	public static function createRefCopy(ParserStrStream $oldObj) {
		$newObj = new self('');
		$newObj->setAsRefObj($oldObj);
		
		return $newObj;
	}

	/**
	 * String length
	 */
	public function len() {
		return $this->len;
	}
	
	/**
	 * Returns the current position.
	 */
	public function pos() {
		return $this->pos;
	}
	
	/**
	 * Returns the character at the current position.
	 */
	public function cur() {
		return $this->chars[$this->pos];
	}
	
	/**
	 * Moves the internal cursor forward.
	 * @return Nothing (= NULL) unless the next position would be invalid.
	 */
	public function moveNext($i = 1) {
		$this->pos += $i;
		if ($this->pos >= $this->len - 1) {
				return false;
		}
	}
	
	/**
	 * Moves the internal cursor forward and returns the current character afterwards.
	 * @return The next character unless the next position would be invalid.
	 */
	public function next() {
		if ($this->moveNext() === false) {
			return false;
		}
		return $this->cur();
	}
	
	/**
	 * Moves the internal cursor backward.
	 * @return Nothing (= NULL) unless the previous position would be invalid.
	 */
	public function movePrev($i = 1) {
		$this->pos -= $i;
		if ($this->pos <= 0) {
			return false;
		}
	}
	
	/**
	 * Moves the internal cursor backwards and returns the current character afterwards.
	 * @return The previous character unless the previous position would be invalid.
	 */
	public function prev() {
		if ($this->movePrev() === false) {
			return false;
		}
		return $this->cur();
	}
}