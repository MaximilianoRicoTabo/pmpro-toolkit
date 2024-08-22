<?php

/**
 *  DTO class for Group and Levels
 */
class GroupDTOClass {

	/**
	 * @var StdClass A group object
	 * @since TBD
	 */
	private $group;

	/**
	 * @var array A set of  Level objects
	 * @since TBD
	 */
	private $levels;

	/**
	 * GroupDTOClass constructor.
	 *
	 * @param StdClass $group A group object
	 * @param array $levels A set of Level objects
	 */
	function __construct( $group, $levels ) {
		$this->group = $group;
		$this->levels = $levels;
	}
}