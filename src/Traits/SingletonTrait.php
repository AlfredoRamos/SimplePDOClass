<?php namespace AlfredoRamos\PDODb\Traits;

/**
 * PDODb - A simple PDO wrapper
 * @link https://github.com/AlfredoRamos/pdodb
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @copyright (c) 2013 Alfredo Ramos
 * @license GNU GPL 3.0+ <https://www.gnu.org/licenses/gpl-3.0.txt>
 */

/**
 * @ignore
 */
if (!defined('IN_PDODB')) {
	exit;
}

trait SingletonTrait {

	private static $instance = null;

	final private function __construct() {
		static::init();
	}

	final private function __clone() {}
	final private function __wakeup() {}

	final public static function instance() {
		if (static::$instance === null) {
			static::$instance = new static;
		}
		return static::$instance;
	}

	protected function init() {}

}
