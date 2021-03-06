<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Validation\Rule;

use Phproberto\Joomla\Entity\Validation\Rule\CustomRule;

/**
 * CustomRule tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class CustomRuleTest extends \TestCase
{
	/**
	 * passes returns correct value.
	 *
	 * @return  void
	 */
	public function testPassesReturnsCorrectValue()
	{
		$rule = new CustomRule(
			function ($value)
			{
				return in_array($value, array('validValue1', 'validValue2'));
			}
		);

		$this->assertTrue($rule->passes('validValue1'));
		$this->assertTrue($rule->passes('validValue2'));
		$this->assertFalse($rule->passes('invalidValue'));
		$this->assertFalse($rule->passes(''));
		$this->assertFalse($rule->passes(null));
	}
}
