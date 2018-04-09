<?php
namespace Lelesys\Plugin\Newsletter\Domain\Repository;

/*
 * This script belongs to the package "Lelesys.Plugin.Newsletter".         *
 *                                                                         *
 * It is free software; you can redistribute it and/or modify it under     *
 * the terms of the GNU Lesser General Public License, either version 3    *
 * of the License, or (at your option) any later version.                  *
 *                                                                         */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\Doctrine\Repository;
use Neos\Utility\ObjectAccess;

/**
 * The Newsletter Repository
 *
 * @Flow\Scope("singleton")
 */
class NewsletterRepository extends Repository {

	/**
	 * All newsletters by given recipient
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person $person Person object
	 * @return array The query result
	 */
	public function getNewslettersByRecipient(\Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person $person) {
		$query = $this->entityManager->createQuery('SELECT n FROM \Lelesys\Plugin\Newsletter\Domain\Model\Newsletter n JOIN n.recipients r WHERE r.Persistence_Object_Identifier=\'' . $person->getUuid() . '\'')
				->execute();
		return $query;
	}

	/**
	 * All newsletters by given recipient group
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
	 * All newsletters by given category
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Category $category Category
	 * @return array The query result
	 */
	public function getNewslettersByCategory(\Lelesys\Plugin\Newsletter\Domain\Model\Category $category) {
		$query = $this->createQuery();
		return $query->matching(
								$query->contains('categories', $category)
						)
						->execute();
	}

}
?>