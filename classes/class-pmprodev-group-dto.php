<?php

/**
 *  DTO class for Group and Levels
 */
class PMProDev_GroupDTOClass {

	/**
	 * @var StdClass A group object
	 * @since TBD
	 */
	public $group;

	/**
	 * @var array A set of  Level objects
	 * @since TBD
	 */
	public $levels;

	/**
	 * PMProDev_GroupDTOClass constructor.
	 *
	 * @param StdClass $group A group object
	 * @param array $levels A set of Level objects
	 */
	function __construct( $group, $levels ) {
		$this->group = $group;
		$this->levels = $levels;
	}
}