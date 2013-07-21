<?php
/**
 * A string stream, especially useful for parsers.
 */
class ParserStrStream {
	private $chars;
	private $len;
	private $pos = 0;
	
	public function __construct($str) {
		$this->chars = preg_split('/(?<!^)(?!$)/u', $str);
		$this->len = count($this->chars);
	}
	
	/**
	 * String length
	 */
	public function len() {
		return $this->len;
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
	public function moveNext() {
		if ($this->pos >= $this->len - 1) {
			return false;
		}
		$this->pos++;
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
	public function movePrev() {
		if ($this->pos == 0) {
			return false;
		}
		$this->pos--;
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