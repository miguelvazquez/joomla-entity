<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Core\Traits\Stubs;

use Phproberto\Joomla\Entity\ComponentEntity;
use Phproberto\Joomla\Entity\Core\Traits\HasAcl;

/**
 * Entity to test HasAcl trait.
 *
 * @since  __DEPLOY_VERSION__
 */
class EntityWithAcl extends ComponentEntity
{
	use HasAcl;
}
