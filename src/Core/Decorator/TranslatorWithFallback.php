<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Core\Decorator;

/**
 * Represents a collection of entities.
 *
 * @since   __DEPLOY_VERSION__
 */
class TranslatorWithFallback extends Translator
{
	/**
	 * Translate a column.
	 *
	 * @param   string  $column   Column to translate
	 * @param   mixed   $default  Default value
	 *
	 * @return  mixed
	 */
	public function translate($column, $default = null)
	{
		$value = $this->translation()->get($column);

		if (!$this->isValidValue($value, $column))
		{
			$value = $this->entity->get($column);
		}

		return $this->isValidValue($value, $column) ? $value : $default;
	}
}
