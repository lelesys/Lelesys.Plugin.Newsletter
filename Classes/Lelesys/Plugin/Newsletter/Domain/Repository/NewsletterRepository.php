<?php

namespace Lelesys\Plugin\Newsletter\Domain\Repository;

/* *
 * This script belongs to the package "Lelesys.Plugin.Newsletter".         *
 *                                                                         *
 * It is free software; you can redistribute it and/or modify it under     *
 * the terms of the GNU Lesser General Public License, either version 3    *
 * of the License, or (at your option) any later version.                  *
 *                                                                         */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Persistence\Doctrine\Repository;
use TYPO3\Flow\Reflection\ObjectAccess;

/**
 * @Flow\Scope("singleton")
 */
class NewsletterRepository extends Repository {

	/**
	 * All newsletter by given recipient
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person $person
	 * @return array The query result
	 */
	public function getNewslettersByRecipient(\Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person $person) {
		$query = $this->entityManager->createQuery('SELECT n FROM \Lelesys\Plugin\Newsletter\Domain\Model\Newsletter n JOIN n.recipients r WHERE r.Persistence_Object_Identifier=\'' . $person->getUuid() . '\'')
				->execute();
		return $query;
	}

	/**
	 * All newsletter by given recipient group
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\AbstractGroup $recipientGroup Recipient Group
	 * @return array The query result
	 */
	public function getNewslettersByRecipientGroup(\Lelesys\Plugin\Newsletter\Domain\Model\Recipient\AbstractGroup $recipientGroup) {
		$query = $this->entityManager->createQuery('SELECT n FROM \Lelesys\Plugin\Newsletter\Domain\Model\Newsletter n JOIN n.recipientGroups r WHERE r.Persistence_Object_Identifier=\'' . $recipientGroup->getUuid() . '\'')
				->execute();
		return $query;
	}

	/**
	 * All newsletter by given category
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Category $category Category
	 * @return array The query result
	 */
	public function getNewslettersByCategory(\Lelesys\Plugin\Newsletter\Domain\Model\Category $category) {
		$query = $this->entityManager->createQuery('SELECT n FROM \Lelesys\Plugin\Newsletter\Domain\Model\Newsletter n JOIN n.categories c WHERE c.Persistence_Object_Identifier=\'' . $category->getUuid() . '\'')
				->execute();
		return $query;
	}

}

?>