<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Core\Traits;

use Phproberto\Joomla\Entity\Core\Column;

defined('JPATH_PLATFORM') || die;

/**
 * Trait for entities that have an associated publish down column.
 *
 * @since  __DEPLOY_VERSION__
 */
trait HasPublishDown
{
	/**
	 * Get the publish down date.
	 *
	 * @return  string
	 */
	public function getPublishDown()
	{
		return $this->get($this->columnAlias(Column::PUBLISH_DOWN));
	}

	/**
	 * Has this entity a publish down date?
	 *
	 * @return  boolean
	 */
	public function hasPublishDown()
	{
		$publishDown = $this->getPublishDown();

		return !empty($publishDown) && $publishDown !== $this->nullDate();
	}

	/**
	 * Check if this entity is published down.
	 *
	 * @return  boolean
	 */
	public function isPublishedDown()
	{
		if (!$this->hasPublishDown())
		{
			return false;
		}

		return \JFactory::getDate($this->getPublishDown()) <= \JFactory::getDate();
	}
}