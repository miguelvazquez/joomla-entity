<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Acl\Stubs;

use Phproberto\Joomla\Entity\ComponentEntity;
use Phproberto\Joomla\Entity\Acl\Traits\HasAcl;
use Phproberto\Joomla\Entity\Core\Traits\HasState;
use Phproberto\Joomla\Entity\Acl\Contracts\Aclable;
use Phproberto\Joomla\Entity\Core\Contracts\Publishable;

/**
 * Entity to test Acl decorator.
 *
 * @since  __DEPLOY_VERSION__
 */
class PublishableEntityWithAcl extends ComponentEntity implements Aclable, Publishable
{
	use HasAcl, HasState;
}
