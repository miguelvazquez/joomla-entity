<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity;

use Joomla\Registry\Registry;
use Phproberto\Joomla\Traits;
use Phproberto\Joomla\Entity\Traits as EntityTraits;
use Phproberto\Joomla\Entity\Exception\InvalidEntityData;
use Phproberto\Joomla\Entity\Exception\LoadEntityDataError;

/**
 * Entity class.
 *
 * @since   __DEPLOY_VERSION__
 */
abstract class Entity implements EntityInterface
{
	use EntityTraits\HasEvents;
	use Traits\HasInstances;

	/**
	 * Identifier.
	 *
	 * @var  integer
	 */
	protected $id;

	/**
	 * Database row data.
	 *
	 * @var  array
	 */
	protected $row;

	/**
	 * Constructor.
	 *
	 * @param   integer  $id  Identifier
	 */
	public function __construct($id = null)
	{
		$this->id = (int) $id;
	}

	/**
	 * Get all the entity properties.
	 *
	 * @return  array
	 */
	public function all()
	{
		if (empty($this->row[$this->primaryKey()]))
		{
			$this->fetch();
		}

		return $this->row;
	}

	/**
	 * Assign a value to entity property.
	 *
	 * @param   string  $property  Name of the property to set
	 * @param   mixed   $value     Value to assign
	 *
	 * @return  self
	 */
	public function assign($property, $value)
	{
		if (null === $this->row)
		{
			$this->row = array();
		}

		$this->row[$property] = $value;

		if ($property === $this->primaryKey())
		{
			$this->id = (int) $value;
		}

		return $this;
	}

	/**
	 * Bind data to the entity.
	 *
	 * @param   mixed  $data  array | \stdClass Data to bind
	 *
	 * @return  self
	 */
	public function bind($data)
	{
		if (!is_array($data) && !$data instanceof \stdClass)
		{
			throw new \InvalidArgumentException(sprintf("Invalid data sent for %s::%s()", __CLASS__, __FUNCTION__));
		}

		$data = (array) $data;

		if (null === $this->row)
		{
			$this->row = array();
		}

		$primaryKey = $this->primaryKey();

		foreach ($data as $property => $value)
		{
			$this->row[$property] = $value;

			if ($property === $primaryKey)
			{
				$this->id = (int) $data[$primaryKey];
			}
		}

		return $this;
	}

	/**
	 * Get an \JDate object from an entity date property.
	 *
	 * @param   string   $property   Name of the property to use as source date
	 * @param   mixed    $tz         Time zone to be used for the date. Special cases:
	 *                               	* boolean true for user setting
	 *                               	* boolean false for server setting.
	 *
	 * @return  \JDate
	 *
	 * @throws  \RuntimeException  If date property is empty
	 */
	public function date($property, $tz = true)
	{
		$dateString = $this->get($property);

		if (empty($dateString))
		{
			$msg = sprintf('Date property `%s` is empty', $property);

			throw new \RuntimeException($msg);
		}

		// UTC date converted to user time zone.
		if ($tz === true)
		{
			$date = \JFactory::getDate($dateString, 'UTC');
			$date->setTimezone($this->joomlaUser()->getTimezone());

			return $date;
		}

		// UTC date converted to server time zone.
		if ($tz === false)
		{
			$config = \JFactory::getConfig();

			$date = \JFactory::getDate($dateString, 'UTC');
			$date->setTimezone(new \DateTimeZone($config->get('offset')));

			return $date;
		}

		// No date conversion.
		if ($tz === null)
		{
			return \JFactory::getDate($dateString);
		}

		// Get a date object based on UTC.
		$date = \JFactory::getDate($dateString, 'UTC');
		$date->setTimezone(new \DateTimeZone($tz));

		return $date;
	}

	/**
	 * Get the component associated to this entity.
	 *
	 * @return  string
	 */
	public function entityComponent()
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
	 * Get this entity name.
	 *
	 * @return  string
	 */
	public function entityName()
	{
		$class = get_class($this);

		if (false !== strpos($class, '\\'))
		{
			$suffix = rtrim(strstr($class, 'Entity'), '\\');
			$parts = explode("\\", $suffix);

			return $parts ? strtolower(end($parts)) : null;
		}

		$parts = explode('Entity', $class, 2);

		return $parts ? strtolower(end($parts)) : null;
	}

	/**
	 * Fetch DB data.
	 *
	 * @return  self
	 */
	public function fetch()
	{
		$this->row = array_merge((array) $this->row, $this->fetchRow());

		return $this;
	}

	/**
	 * Load the entity from the database.
	 *
	 * @return  array
	 *
	 * @throws  LoadEntityDataError  Table error loading row
	 * @throws  InvalidEntityData    Incorrect data received
	 */
	protected function fetchRow()
	{
		$table = $this->table();

		if (!$table->load($this->id))
		{
			throw LoadEntityDataError::tableError($this, $table->getError());
		}

		$data = $table->getProperties(true);

		if (empty($data))
		{
			throw InvalidEntityData::emptyData($this);
		}

		if (!array_key_exists($this->primaryKey(), $data))
		{
			throw InvalidEntityData::missingPrimaryKey($this);
		}

		$this->id = (int) $data[$this->primaryKey()];

		return $data;
	}

	/**
	 * Get a property of this entity.
	 *
	 * @param   string  $property  Name of the property to get
	 * @param   mixed   $default   Value to use as default if property is null
	 *
	 * @return  mixed
	 *
	 * @throws  \InvalidArgumentException  Property does not exist
	 */
	public function get($property, $default = null)
	{
		$data = $this->all();

		if (!array_key_exists($property, $data))
		{
			$msg = sprintf('Property `%s` does not exist', $property);

			throw new \InvalidArgumentException($msg);
		}

		if (null === $data[$property])
		{
			return $default;
		}

		return $data[$property];
	}

	/**
	 * Get the \JDatabaseDriver object.
	 *
	 * @return  \JDatabaseDriver  Internal database driver object.
	 */
	public function getDbo()
	{
		return $this->table()->getDbo();
	}

	/**
	 * Check if entity has a property.
	 *
	 * @param   string   $property  Entity property name
	 *
	 * @return  boolean
	 */
	public function has($property)
	{
		$row = $this->all();

		return $row && array_key_exists($property, $row);
	}

	/**
	 * Check if this entity has an id.
	 *
	 * @return  boolean
	 */
	public function hasId()
	{
		return !empty($this->id);
	}

	/**
	 * Gets the Identifier.
	 *
	 * @return  integer
	 */
	public function id()
	{
		return $this->id;
	}

	/**
	 * Load an instance.
	 *
	 * @param   integer  $id  Instance identifier
	 *
	 * @return  static
	 */
	public static function load($id)
	{
		return static::instance($id)->fetch();
	}

	/**
	 * Check if entity has been loaded.
	 *
	 * @return  boolean
	 */
	public function isLoaded()
	{
		return $this->hasId() && !empty($this->row);
	}

	/**
	 * \JFactory::getUser() proxy for testing purposes
	 *
	 * @param   integer  $id  The user to load - Can be an integer or string - If string, it is converted to ID automatically.
	 *
	 * @return  \JUser object
	 *
	 * @see     \JUser
	 *
	 * @codeCoverageIgnore
	 */
	protected function joomlaUser($id = null)
	{
		return \JFactory::getUser($id);
	}

	/**
	 * Get the content of a column with data stored in JSON.
	 *
	 * @param   string  $property  Name of the column storing data
	 *
	 * @return  array
	 */
	public function json($property)
	{
		$data = array();
		$row  = $this->all();

		if (empty($row[$property]))
		{
			return $data;
		}

		foreach ((array) json_decode($row[$property]) as $property => $value)
		{
			if ($value === '')
			{
				continue;
			}

			$data[$property] = $value;
		}

		return $data;
	}

	/**
	 * Get entity primary key column.
	 *
	 * @return  string
	 */
	public function primaryKey()
	{
		return 'id';
	}

	/**
	 * Get a Registry object from a property of the entity.
	 *
	 * @param   string  $property  Property containing the data to import
	 *
	 * @return  Registry
	 *
	 * @throws  \InvalidArgumentException  Property does not exist
	 */
	public function registry($property)
	{
		return new Registry($this->get($property));
	}

	/**
	 * Save entity to the database.
	 *
	 * @return  boolean
	 */
	public function save()
	{
		$table = $this->table();

		if (!$table->save($this->row))
		{
			throw new \RuntimeException($table->getError());
		}

		return true;
	}

	/**
	 * Get an entity date field formatted.
	 *
	 * @param   string   $property   Name of the property to use as source date
	 * @param   strig    $format     Format to output the date. PHP format | language string
	 * @param   array    $options    Optional settings:
	 *                               gregorian => True to use Gregorian calendar.
	 * 	                             tz => Time zone to be used for the date.  Special cases:
	 * 	                             	* boolean true for user setting
	 * 	                              	* boolean false for server setting.
	 *
	 * @return  string
	 */
	public function showDate($property, $format = 'DATE_FORMAT_LC1', array $options = array())
	{
		$tz        = isset($options['tz']) ? $options['tz'] : true;
		$gregorian = isset($options['gregorian']) ? $options['gregorian'] : false;

		$date = $this->date($property, $tz);

		if (\JFactory::getLanguage()->hasKey($format))
		{
			$format = \JText::_($format);
		}

		return $gregorian ? $date->format($format, true) : $date->calendar($format, true);
	}

	/**
	 * Get a table.
	 *
	 * @param   string  $name     Table name. Optional.
	 * @param   string  $prefix   Class prefix. Optional.
	 * @param   array   $options  Configuration array for the table. Optional.
	 *
	 * @return  \JTable
	 *
	 * @throws  \InvalidArgumentException
	 *
	 * @codeCoverageIgnore
	 */
	public function table($name = '', $prefix = null, $options = array())
	{
		$table = \JTable::getInstance($name, $prefix);

		if (!$table instanceof \JTable)
		{
			throw new \InvalidArgumentException(
				sprintf("Cannot find the table `%s`.", $prefix . $name)
			);
		}

		return $table;
	}

	/**
	 * Unassigns a row property.
	 *
	 * @param   string  $property  Name of the property to set
	 *
	 * @return  self
	 */
	public function unassign($property)
	{
		unset($this->row[$property]);

		return $this;
	}
}
