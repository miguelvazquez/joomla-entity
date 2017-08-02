<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Core\Traits;

use Phproberto\Joomla\Entity\Core\Extension\Component;

/**
 * Trait for entities with an associated component.
 *
 * @since   __DEPLOY_VERSION__
 */
trait HasComponent
{
	/**
	 * Entity component
	 *
	 * @var  Component
	 */
	protected $component;

	/**
	 * Retrieve the associated component.
	 *
	 * @return  Component
	 */
	public function component()
	{
		if (null === $this->component)
		{
			$this->component = $this->loadComponent();
		}

		return $this->component;
	}

	/**
	 * Try to guess component option from class prefix
	 *
	 * @return  mixed  null (not found) | string (found)
	 */
	protected function componentOption()
	{
		$class = get_class($this);

		if (false !== strpos($class, '\\'))
		{
			$suffix = rtrim(strstr($class, 'Entity'), '\\');
			$parts = explode("\\", $suffix);

			return array_key_exists(1, $parts) ? 'com_' . strtolower($parts[1]) : null;
		}

		return  'com_' . strtolower(strstr($class, 'Entity', true));
	}

	/**
	 * Load associated component
	 *
	 * @return  Component
	 *
	 * @throws  \InvalidArgumentException  Wrong option received
	 * @throws  \RuntimeException          Component not found
	 */
	protected function loadComponent()
	{
		return Component::fromOption($this->componentOption());
	}
}