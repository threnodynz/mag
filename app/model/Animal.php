<?php
/*
 * MIT License
 *
 * Copyright (c) 2016 Matt Biddle
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Model;
use Common\Helper;


abstract class Animal
{
	protected $type;
	protected $health;
	protected $fatalHealthLevel;
    protected $state;
    // Flag to denote an animal's health has reached <= $fatalHealthLevel.
    protected $hasReachedFatalHealthLevel;

	// Force use of the factory method.
	protected function __construct($type = 'animal', $fatalHealthLevel = 0)
	{
		$this->health           = 100;
		$this->type             = $type;
		$this->fatalHealthLevel = $fatalHealthLevel;
        $this->state            = Helper::STATE_ALIVE;
        $this->hasReachedFatalHealthLevel = false;
	}

	public static function factory()
	{
		// Returns a generic Animal object using the default constructor arguments.
		return new static();
	}

	// Make sure we have external read access to models properties and getter methods.
	public function __get($property)
	{
		if(isset($this->$property)) {
			return $this->$property;
		}
		else if(method_exists($this, 'get_' . ucwords($property))) {
			return $this-> {'get_' . $property} ();
		}
		else {
			throw new \Exception("Unknown property '$property' in model " . get_class($this));
		}
	}

	public function increaseHealth($value) {
		$level = $this->health + $value;

		if($level >= 100) { // Cap maximum health level at 100 percent.
			$this->health = 100;
		}
		else {
			$this->health = $level;

            if (is_a($this, 'Model\Elephant') && $this->hasReachedFatalHealthLevel === true) {
                $this->hasReachedFatalHealthLevel = false;
                $this->state = Helper::STATE_ALIVE;
            }
        }
	}

	public function decreaseHealth($value) {
		$level = $this->health - $value;

		if($level < 0) { // Cap minimum health level at 0 percent.
			$this->health = 0;
		}
		else {
			$this->health = $level;
		}

		// Check if the animal's health value is at or below its fatal level. Pronounce it dead if it is.
        // Exception to the rule: when an Elephant has a health below 70%, it cannot walk.
        //    If its health does not return above 70% once the subsequent hour has elapsed, it is pronounced dead.
		if ($this->health <= $this->fatalHealthLevel) {
            if (is_a($this, 'Model\Elephant')) {

                if ($this->hasReachedFatalHealthLevel === false) {
                    $this->hasReachedFatalHealthLevel = true;
                    $this->state = Helper::STATE_CANNOT_WALK;
                } else {
                    $this->state = Helper::STATE_DEAD;
                }

            } else {
                $this->state = Helper::STATE_DEAD;
            }
        }
	}

    public function getFormattedState() {
        return Helper::getFormattedState($this->state);
    }

}