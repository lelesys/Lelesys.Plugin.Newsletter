<?php
namespace Lelesys\Plugin\Newsletter\Domain\Repository\Recipient;

/*                                                                         *
 * This script belongs to the package "Lelesys.Plugin.Newsletter".         *
 *                                                                         *
 * It is free software; you can redistribute it and/or modify it under     *
 * the terms of the GNU Lesser General Public License, either version 3    *
 * of the License, or (at your option) any later version.                  *
 *                                                                         */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Persistence\Doctrine\Repository;

/**
 * @Flow\Scope("singleton")
 */
class PersonRepository extends Repository {

	/**
	 * gets existing user
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person $newPerson Person object
	 * @return array The query result
	 */
	public function isExistingUser(\Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person $newPerson) {
		$query = $this->entityManager->createQuery('SELECT r FROM \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person r JOIN r.primaryElectronicAddress email WHERE email.identifier=\'' . $newPerson->getPrimaryElectronicAddress()->getIdentifier() . '\'')
				->execute();
		return $query;
	}

	/**
	 * Get all approved user
	 *
	 * @return array The query result
	 */
	public function findAllApprovedUsers() {
		$query = $this->entityManager->createQuery('SELECT r FROM \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person r JOIN r.primaryElectronicAddress email WHERE email.approved=1 AND r.subscribedToNewsletter = 1')
				->execute();
		return $query;
	}

	/**
	 * All recipients by given category
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Category $category Category
	 * @return \TYPO3\FLOW3\Persistence\QueryResultInterface The query result
	 */
	public function getRecipientsByCategory(\Lelesys\Plugin\Newsletter\Domain\Model\Category $category) {
		$query = $this->createQuery();
		return $query->matching(
								$query->contains('categories', $category)
						)
						->execute();
	}

}
?>