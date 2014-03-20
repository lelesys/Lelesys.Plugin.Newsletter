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
use TYPO3\Flow\Persistence\Repository;

/**
 * The EmailLog Repository
 *
 * @Flow\Scope("singleton")
 */
class EmailLogRepository extends \TYPO3\Flow\Persistence\Doctrine\Repository {

	/**
	 * Finds recipient email address
	 *
	 * @param string $identifier Person identifier
	 * @return array
	 */
	public function getRecipientEmail($identifier) {
		$dql = 'SELECT pe.identifier FROM \TYPO3\Party\Domain\Model\Person p inner join p.primaryElectronicAddress pe where p.Persistence_Object_Identifier= \'' . $identifier . '\'';
		$query = $this->entityManager->createQuery($dql);
		return $query->execute();
	}

	/**
	 * Find all EmailLogs for undelivered mailes(isSent=FALSE)
	 * Find all logs which has value isFailed=TRUE
	 *
	 * @param integer $limit Limit of emails
	 * @param integer $offset Offset of emails
	 * @return TYPO3\Flow\Persistence\QueryResultInterface The query result
	 */
	public function findAllUndeliveredMailsLogs($limit, $offset) {
		$query = $this->createQuery();
		return $query->matching(
								$query->logicalOr(
										$query->equals('isFailed', '1'), $query->equals('isSent', '0')
								)
						)
						->setLimit($limit)
						->setOffset($offset)
						->execute();
	}

	/**
	 * Find total number of mails to be sent.
	 *
	 * @return integer Number of mails to be sent
	 */
	public function findCountOfMailsLogs() {
		$query = $this->createQuery();
		return $query->matching(
								$query->logicalOr(
										$query->equals('isFailed', '1'), $query->equals('isSent', '0')
								)
						)
						->execute()->count();
	}

}
?>