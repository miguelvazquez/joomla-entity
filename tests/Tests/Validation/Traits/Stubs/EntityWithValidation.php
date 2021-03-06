<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Validation\Traits\Stubs;

use Phproberto\Joomla\Entity\ComponentEntity;
use Phproberto\Joomla\Entity\Validation\Traits\HasValidation;
use Phproberto\Joomla\Entity\Validation\Contracts\Validable;

/**
 * Entity to test HasValidation trait.
 *
 * @since  __DEPLOY_VERSION__
 */
class EntityWithValidation extends ComponentEntity implements Validable
{
	use HasValidation;
}
