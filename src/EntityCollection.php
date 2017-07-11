<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity;

use Phproberto\Joomla\Entity\EntityInterface;

/**
 * Represents a collection of entities.
 *
 * @since   __DEPLOY_VERSION__
 */
class EntityCollection implements \Countable, \Iterator
{
	/**
	 * @var  array
	 */
	protected $entities = array();

	/**
	 * Constructor.
	 *
	 * @param   EntityInterface[]  $entities  Entities to initialise the collection
	 */
	public function __construct(array $entities = array())
	{
		if ($entities)
		{
			foreach ($entities as $entity)
			{
				$this->add($entity);
			}
		}
	}

	/**
	 * Adds an entity to the collection.
	 * Note: It won't overwrite existing entities.
	 *
	 * @param   EntityInterface  $entity  Entity going to be added
	 *
	 * @return  boolean
	 *
	 * @throws  \InvalidArgumentException
	 */
	public function add(EntityInterface $entity)
	{
		return $this->write($entity, false);
	}

	/**
	 * Clears all the entities of the collection.
	 *
	 * @return  self
	 */
	public function clear()
	{
		$this->entities = array();

		return $this;
	}

	/**
	 * Get the count of entities in this collection.
	 *
	 * @return  integer
	 */
	public function count()
	{
		return count($this->entities);
	}

	/**
	 * Get the active entity.
	 * Part of the iterator implementation.
	 *
	 * @return  mixed  EntityInterface | FALSE if no entities
	 */
	public function current()
	{
		return current($this->entities);
	}

	/**
	 * Get an entity by it's id
	 *
	 * @param   integer  $id  Item's identifier
	 *
	 * @return  mixed  EntityInterface if item exists. Null otherwise
	 */
	public function get($id)
	{
		if (!$this->has($id))
		{
			throw new \InvalidArgumentException(sprintf('Error in %s::%s(): Collection does not have %s element', __CLASS__, __FUNCTION__, $id));
		}

		return $this->entities[$id];
	}

	/**
	 * Check if an entity is present in this collection.
	 *
	 * @param   integer  $id  Entity identifier
	 *
	 * @return  boolean
	 */
	public function has($id)
	{
		return isset($this->entities[$id]);
	}

	/**
	 * Returns ids of the entities in this collection in the order they were added.
	 *
	 * @return  array
	 */
	public function ids()
	{
		return array_keys($this->entities);
	}

	/**
	 * Check if the collection is empty.
	 *
	 * @return  boolean
	 */
	public function isEmpty()
	{
		return !$this->entities;
	}

	/**
	 * Return the id of the active entity.
	 * Part of the iterator implementation.
	 *
	 * @return  mixed  integer | null for no entities
	 */
	public function key()
	{
		return key($this->entities);
	}

	/**
	 * Gets the next entity.
	 * Part of the iterator implementation.
	 *
	 * @return  mixed  EntityInterface | FALSE if no entities
	 */
	public function next()
	{
		return next($this->entities);
	}

	/**
	 * Remove an entity from the collection.
	 *
	 * @param   integer  $id  Entity identifier
	 *
	 * @return  boolean
	 */
	public function remove($id)
	{
		if (!$this->has($id))
		{
			return false;
		}

		unset($this->entities[$id]);

		return true;
	}

	/**
	 * Get the first entity in the collection.
	 * Part of the iterator implementation.
	 *
	 * @return  mixed  EntityInterface | FALSE if no entities
	 */
	public function rewind()
	{
		return reset($this->entities);
	}

	/**
	 * Check if there are still entities in the entities array.
	 * Part of the iterator implementation.
	 *
	 * @return  boolean
	 */
	public function valid()
	{
		return key($this->entities) !== null;
	}

	/**
	 * Proxy for add with overwrite enabled.
	 *
	 * @param   EntityInterface  $entity     Entity
	 * @param   boolean          $overwrite  Force writing the entity if it already exists
	 *
	 * @return  boolean
	 *
	 * @throws  \InvalidArgumentException
	 */
	public function write(EntityInterface $entity, $overwrite = true)
	{
		$id = (int) $entity->getId();

		if (!$id)
		{
			throw new \InvalidArgumentException("Cannot add entity without id to the collection");
		}

		if (!$overwrite && $this->has($id))
		{
			return false;
		}

		$this->entities[$id] = $entity;

		return true;
	}
}