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
								$query->contains('newsletterCategories', $category)
						)
						->execute();
	}

	/**
	 * All recipients by given recipient group
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\AbstractGroup $recipientGroup Recipient Group
	 * @return array The query result
	 */
	public function getRecipientsByRecipientGroup(\Lelesys\Plugin\Newsletter\Domain\Model\Recipient\AbstractGroup $recipientGroup) {
		$query = $this->entityManager->createQuery('SELECT n FROM \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person n JOIN n.groups r WHERE r.Persistence_Object_Identifier=\'' . $recipientGroup->getUuid() . '\'')
				->execute();
		return $query;
	}

	/**
	 * All recipients by given recipient group
	 *
	 * @param $recipient recipients
	 * @return array The query result
	 */
	public function getRecipientsByNewsletterCategories($recipient) {
		$query = $this->entityManager->createQuery('SELECT n FROM \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person n JOIN n.newsletterCategories r WHERE r.Persistence_Object_Identifier=\'' . $recipient->getUuid() . '\'')
				->execute();
		return $query;
	}


	/**
	 * All recipients by given newsletter category
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Category $category Newsletter category
	 * @return array The query result
	 */
	public function findAllSelectedUsers(\Lelesys\Plugin\Newsletter\Domain\Model\Category $category) {
		$query = $this->entityManager->createQuery('SELECT n FROM \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person n JOIN n.newsletterCategories r WHERE r.Persistence_Object_Identifier=\'' . $category->getUuid() . '\'')
				->execute();
		return $query;
	}

	/**
	 * Find all subscribed user with search
	 *
	 * @param string $keyword Searched string
	 * @return array The query result
	 */
	public function findBySubscribedToNewsletter($keyword) {
		$query = new \TYPO3\Flow\Persistence\Doctrine\Query($this->objectType);
		$queryBuilder = \TYPO3\Flow\Reflection\ObjectAccess::getProperty($query, 'queryBuilder', TRUE);
		$queryBuilder->resetDQLParts();
		$queryBuilder->select('p')
				->from('\Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person', 'p')
				->where('p.subscribedToNewsletter = 1');
		if($keyword != '') {
			$queryBuilder->innerJoin('p.name', 'pn')
						 ->innerJoin('p.primaryElectronicAddress', 'pe')
						 ->andWhere('pn.firstName like \'%' . $keyword . '%\' OR pn.lastName like \'%' . $keyword . '%\' OR pn.fullName like \'%' . $keyword . '%\'');
		}
		return $query->execute();
	}

}
?>