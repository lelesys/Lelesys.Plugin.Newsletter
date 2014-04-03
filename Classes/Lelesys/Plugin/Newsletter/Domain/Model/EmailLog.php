<?php
namespace Lelesys\Plugin\Newsletter\Domain\Model;

/*                                                                         *
 * This script belongs to the package "Lelesys.Plugin.Newsletter".         *
 *                                                                         *
 * It is free software; you can redistribute it and/or modify it under     *
 * the terms of the GNU Lesser General Public License, either version 3    *
 * of the License, or (at your option) any later version.                  *
 *                                                                         */

use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * EmailLog
 *
 * @Flow\Entity
 */
class EmailLog {

	const RECIPIENT_TYPE_STATIC = '\Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Group\StaticList';
	const RECIPIENT_TYPE_PERSON = 'Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person';

	/**
	 * Time Created
	 *
	 * @var \DateTime
	 */
	protected $timeCreated;

	/**
	 * Time Sent
	 *
	 * @var \DateTime
	 * @ORM\Column(nullable=true)
	 */
	protected $timeSent;

	/**
	 * The Newsletter
	 * @var \Lelesys\Plugin\Newsletter\Domain\Model\Newsletter
	 * @ORM\ManyToOne(cascade={"detach","refresh","remove"})
	 */
	protected $newsletter;

	/**
	 * Recipient Type
	 *
	 * @var string
	 */
	protected $recipientType;

	/**
	 * Recipient
	 *
	 * @var string
	 */
	protected $recipient;

	/**
	 * IsSent
	 *
	 * @var boolean
	 */
	protected $isSent;

	/**
	 * IsFailed
	 *
	 * @var boolean
	 */
	protected $isFailed;



	/**
	 * The Constructor for Order
	 */
	public function __construct() {
		$this->timeCreated = new \DateTime();
	}

	/**
	 * Gets Time Created
	 *
	 * @return \DateTime
	 */
	public function getTimeCreated() {
		return $this->timeCreated;
	}

	/**
	 * Sets Time Created
	 *
	 * @param \DateTime $timeCreated Time Created
	 * @return void
	 */
	public function setTimeCreated($timeCreated) {
		$this->timeCreated = $timeCreated;
	}

	/**
	 * Gets Time Sent
	 *
	 * @return \DateTime
	 */
	public function getTimeSent() {
		return $this->timeSent;
	}

	/**
	 * Sets Time Sent
	 *
	 * @param \DateTime $timeSent Time Sent
	 * @return void
	 */
	public function setTimeSent($timeSent) {
		$this->timeSent = $timeSent;
	}

	/**
	 * Gets Newsletter
	 *
	 * @return \Lelesys\Plugin\Newsletter\Domain\Model\Newsletter
	 */
	public function getNewsletter() {
		return $this->newsletter;
	}

	/**
	 * Sets Newsletter
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter Newsletter
	 * @return void
	 */
	public function setNewsletter(\Lelesys\Plugin\Newsletter\Domain\Model\Newsletter $newsletter) {
		$this->newsletter = $newsletter;
	}

	/**
	 * Gets Recipient Type
	 *
	 * @return string
	 */
	public function getRecipientType() {
		return $this->recipientType;
	}

	/**
	 * Sets Recipient Type
	 *
	 * @param string $recipientType Recipient Type
	 * @return void
	 */
	public function setRecipientType($recipientType) {
		$this->recipientType = $recipientType;
	}

	/**
	 * Get Recipient
	 *
	 * @return string
	 */
	public function getRecipient() {
		return $this->recipient;
	}

	/**
	 * Sets Recipient
	 *
	 * @param string $recipient Recipient
	 * @return void
	 */
	public function setRecipient($recipient) {
		$this->recipient = $recipient;
	}

	/**
	 * Gets IsSent
	 *
	 * @return boolean
	 */
	public function getIsSent() {
		return $this->isSent;
	}

	/**
	 * Sets IsSent
	 *
	 * @param boolean $isSent IsSent
	 * @return void
	 */
	public function setIsSent($isSent) {
		$this->isSent = $isSent;
	}

	/**
	 * Gets IsFailed
	 *
	 * @return boolean
	 */
	public function getIsFailed() {
		return $this->isFailed;
	}

	/**
	 * Sets IsFailed
	 *
	 * @param boolean $isFailed IsFailed
	 * @return void
	 */
	public function setIsFailed($isFailed) {
		$this->isFailed = $isFailed;
	}

}
?>