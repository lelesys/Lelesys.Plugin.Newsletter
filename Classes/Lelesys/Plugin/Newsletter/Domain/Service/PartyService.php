<?php
namespace Lelesys\Plugin\Newsletter\Domain\Service;

/* *
 * This script belongs to the package "Lelesys.Plugin.Newsletter".         *
 *                                                                         *
 * It is free software; you can redistribute it and/or modify it under     *
 * the terms of the GNU Lesser General Public License, either version 3    *
 * of the License, or (at your option) any later version.                  *
 *                                                                         */

use TYPO3\Flow\Annotations as Flow;
use Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Group\Party;

/**
 * Party Service
 *
 * @Flow\Scope("singleton")
 */
class PartyService {

	/**
	 * Party Repository
	 *
	 * @Flow\Inject
	 * @var \Lelesys\Plugin\Newsletter\Domain\Repository\Recipient\Group\PartyRepository
	 */
	protected $partyRepository;

	/**
	 * Person Repository
	 *
	 * @Flow\Inject
	 * @var \Lelesys\Plugin\Newsletter\Domain\Repository\Recipient\PersonRepository
	 */
	protected $personRepository;

	/**
	 * Newsletter Service
	 *
	 * @Flow\Inject
	 * @var \Lelesys\Plugin\Newsletter\Domain\Service\NewsletterService
	 */
	protected $newsletterService;

	/**
	 * Inject persistence manager
	 *
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Persistence\PersistenceManagerInterface
	 */
	protected $persistenceManager;

	/**
	 * Person Service
	 *
	 * @Flow\Inject
	 * @var \Lelesys\Plugin\Newsletter\Domain\Service\PersonService
	 */
	protected $personService;

	/**
	 * List of all group parties
	 *
	 * @return \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Group\Party party Party object
	 */
	public function listAll() {
		return $this->partyRepository->findAll();
	}

	/**
	 * Adds new group party
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Group\Party $newParty Party object
	 * @return void
	 */
	public function create(\Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Group\Party $newParty) {
		$this->partyRepository->add($newParty);
		foreach ($newParty->getRecipients() as $recipient) {
			$recipientObject = $this->persistenceManager->getObjectByIdentifier($recipient->getUuid(), '\Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person');
			$recipientObject->addGroup($newParty);
			$this->personRepository->update($recipientObject);
		}
	}

	/**
	 * Updates group party
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Group\Party $party Party object
	 * @return void
	 */
	public function update(\Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Group\Party $party) {
		$this->personService->findByRecipientsByGruops($party);
		foreach ($party->getRecipients() as $recipient) {
			$recipientObject = $this->persistenceManager->getObjectByIdentifier($recipient->getUuid(), '\Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person');
			$recipientObject->addGroup($party);
			$this->personRepository->update($recipientObject);
		}
	}

	/**
	 * Deletes group party
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Group\Party $party Party object
	 * @return void
	 */
	public function delete(\Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Group\Party $party) {
		$this->newsletterService->deleteRelatedRecipientGroupParty($party);
		$this->partyRepository->remove($party);
		$this->persistenceManager->persistAll();
	}

}
?>