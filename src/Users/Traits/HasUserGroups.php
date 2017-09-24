<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Users\Traits;

defined('JPATH_PLATFORM') || die;

/**
 * Trait for entities that have associated user groups.
 *
 * @since  __DEPLOY_VERSION__
 */
trait HasUserGroups
{
	/**
	 * Associated user groups.
	 *
	 * @var  Collection
	 */
	protected $userGroups;

	/**
	 * Clear already loaded userGroups.
	 *
	 * @return  self
	 */
	public function clearUserGroups()
	{
		$this->userGroups = null;

		return $this;
	}

	/**
	 * Get the associated user groups.
	 *
	 * @param   boolean  $reload  Force data reloading
	 *
	 * @return  Collection
	 */
	public function userGroups($reload = false)
	{
		if ($reload || null === $this->userGroups)
		{
			$this->userGroups = $this->loadUserGroups();
		}

		return $this->userGroups;
	}

	/**
	 * Check if this entity has an associated user group.
	 *
	 * @param   integer   $id  User identifier
	 *
	 * @return  boolean
	 */
	public function hasUserGroup($id)
	{
		return $this->userGroups()->has($id);
	}

	/**
	 * Check if this entity has associated user groups.
	 *
	 * @return  boolean
	 */
	public function hasUserGroups()
	{
		return !$this->userGroups()->isEmpty();
	}

	/**
	 * Load associated user groups from DB.
	 *
	 * @return  Collection
	 */
	abstract protected function loadUserGroups();
}