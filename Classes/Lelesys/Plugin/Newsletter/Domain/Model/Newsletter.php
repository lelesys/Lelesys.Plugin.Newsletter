<?php

namespace Lelesys\Plugin\Newsletter\Domain\Model;

/* *
 * This script belongs to the package "Lelesys.Plugin.Newsletter".         *
 *                                                                         *
 * It is free software; you can redistribute it and/or modify it under     *
 * the terms of the GNU Lesser General Public License, either version 3    *
 * of the License, or (at your option) any later version.                  *
 *                                                                         */

use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * Newsletter
 *
 * @Flow\Entity
 */
class Newsletter {

	/**
	 * FromName
	 *
	 * @var string
	 * @ORM\Column(nullable=true)
	 */
	protected $fromName;

	/**
	 * FromEmail
	 *
	 * @var string
	 * @Flow\Validate(type="NotEmpty")
	 * @Flow\Validate(type="EmailAddress")
	 */
	protected $fromEmail;

	/**
	 * ReplyToName
	 *
	 * @var string
	 * @ORM\Column(nullable=true)
	 */
	protected $replyToName;

	/**
	 * ReplyToEmail
	 *
	 * @var string
	 * @ORM\Column(nullable=true)
	 * @Flow\Validate(type="EmailAddress")
	 */
	protected $replyToEmail;

	/**
	 * Organisation
	 *
	 * @var string
	 * @ORM\Column(nullable=true)
	 */
	protected $organisation;

	/**
	 * Subject
	 *
	 * @var string
	 * @ORM\Column(nullable=true)
	 */
	protected $subject;

	/**
	 * Encoding
	 *
	 * @var string
	 * @ORM\Column(nullable=true)
	 */
	protected $encoding;

	/**
	 * CharacterSet
	 *
	 * @var string
	 * @ORM\Column(nullable=true)
	 */
	protected $characterSet;

	/**
	 * HtmlBody
	 *
	 * @var string
	 * @ORM\Column(type="text", nullable=true)
	 */
	protected $htmlBody;

	/**
	 * PlainTextBody
	 *
	 * @var string
	 * @ORM\Column(type="text", nullable=true)
	 */
	protected $plainTextBody;

	/**
	 * Priority
	 *
	 * @var integer
	 * @ORM\Column(nullable=true)
	 */
	protected $priority;

	/**
	 * Attachments
	 *
	 * @var \TYPO3\Flow\Resource\Resource
	 * @ORM\ManyToOne(cascade={"persist", "detach"})
	 * @ORM\Column(nullable=true)
	 */
	protected $attachments;

	/**
	 * IsSent
	 *
	 * @var boolean
	 * @ORM\Column(nullable=true)
	 */
	protected $isSent;

	/**
	 * ContentNode
	 *
	 * @var string
	 * @ORM\Column(nullable=true)
	 */
	protected $contentNode;

	/**
	 * Categories
	 *
	 * @var \Doctrine\Common\Collections\Collection<\Lelesys\Plugin\Newsletter\Domain\Model\Category>
	 * @ORM\ManyToMany(cascade={"persist", "detach"})
	 * @ORM\Column(nullable=true)
	 */
	protected $categories;

	/**
	 * Recipient Groups
	 *
	 * @var \Doctrine\Common\Collections\Collection<\Lelesys\Plugin\Newsletter\Domain\Model\Recipient\AbstractGroup>
	 * @ORM\ManyToMany(cascade={"persist", "detach"})
	 * @ORM\Column(nullable=true)
	 */
	protected $recipientGroups;

	/**
	 * Recipients
	 *
	 * @var \Doctrine\Common\Collections\Collection<\Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person>
	 * @ORM\ManyToMany(cascade={"persist", "detach"})
	 * @ORM\Column(nullable=true)
	 */
	protected $recipients;

	/**
	 * Constructor
	 *
	 * @return void
	 */
	public function __construct() {
		$this->recipients = new \Doctrine\Common\Collections\ArrayCollection();
		$this->recipientGroups = new \Doctrine\Common\Collections\ArrayCollection();
		$this->categories = new \Doctrine\Common\Collections\ArrayCollection();
	}

	/**
	 * Gets FromName
	 *
	 * @return string
	 */
	public function getFromName() {
		return $this->fromName;
	}

	/**
	 * Sets FromName
	 *
	 * @param string $fromName
	 * @return void
	 */
	public function setFromName($fromName) {
		$this->fromName = $fromName;
	}

	/**
	 * Gets FromEmail
	 *
	 * @return string
	 */
	public function getFromEmail() {
		return $this->fromEmail;
	}

	/**
	 * Sets FromEmail
	 *
	 * @param string $fromEmail
	 * @return void
	 */
	public function setFromEmail($fromEmail) {
		$this->fromEmail = $fromEmail;
	}

	/**
	 * Gets ReplyToName
	 *
	 * @return string
	 */
	public function getReplyToName() {
		return $this->replyToName;
	}

	/**
	 * Sets ReplyToName
	 *
	 * @param string $replyToName
	 * @return void
	 */
	public function setReplyToName($replyToName) {
		$this->replyToName = $replyToName;
	}

	/**
	 * Gets ReplyToEmail
	 *
	 * @return string
	 */
	public function getReplyToEmail() {
		return $this->replyToEmail;
	}

	/**
	 * Sets ReplyToEmail
	 *
	 * @param string $replyToEmail
	 * @return void
	 */
	public function setReplyToEmail($replyToEmail) {
		$this->replyToEmail = $replyToEmail;
	}

	/**
	 * Gets Organisation
	 *
	 * @return string
	 */
	public function getOrganisation() {
		return $this->organisation;
	}

	/**
	 * Sets Organisation
	 *
	 * @param string $organisation
	 * @return void
	 */
	public function setOrganisation($organisation) {
		$this->organisation = $organisation;
	}

	/**
	 * Gets Subject
	 *
	 * @return string
	 */
	public function getSubject() {
		return $this->subject;
	}

	/**
	 * Sets Subject
	 *
	 * @param string $subject
	 * @return void
	 */
	public function setSubject($subject) {
		$this->subject = $subject;
	}

	/**
	 * Gets Encoding
	 *
	 * @return string
	 */
	public function getEncoding() {
		return $this->encoding;
	}

	/**
	 * Sets Encoding
	 *
	 * @param string $encoding
	 * @return void
	 */
	public function setEncoding($encoding) {
		$this->encoding = $encoding;
	}

	/**
	 * Gets CharacterSet
	 *
	 * @return string
	 */
	public function getCharacterSet() {
		return $this->characterSet;
	}

	/**
	 * Sets CharacterSet
	 *
	 * @param string $characterSet
	 * @return void
	 */
	public function setCharacterSet($characterSet) {
		$this->characterSet = $characterSet;
	}

	/**
	 * Gets HtmlBody
	 *
	 * @return string
	 */
	public function getHtmlBody() {
		return $this->htmlBody;
	}

	/**
	 * Sets HtmlBody
	 *
	 * @param string $htmlBody
	 * @return void
	 */
	public function setHtmlBody($htmlBody) {
		$this->htmlBody = $htmlBody;
	}

	/**
	 * Gets PlainTextBody
	 *
	 * @return string
	 */
	public function getPlainTextBody() {
		return $this->plainTextBody;
	}

	/**
	 * Sets PlainTextBody
	 *
	 * @param string $plainTextBody
	 * @return void
	 */
	public function setPlainTextBody($plainTextBody) {
		$this->plainTextBody = $plainTextBody;
	}

	/**
	 * Gets Priority
	 *
	 * @return integer
	 */
	public function getPriority() {
		return $this->priority;
	}

	/**
	 * Sets Priority
	 *
	 * @param integer $priority
	 * @return void
	 */
	public function setPriority($priority) {
		$this->priority = $priority;
	}

	/**
	 * Gets Attachments
	 *
	 * @return \TYPO3\Flow\Resource\Resource The Newsletter's attachments
	 */
	public function getAttachments() {
		return $this->attachments;
	}

	/**
	 * Sets Attachments
	 *
	 * @param \TYPO3\Flow\Resource\Resource $attachments
	 * @return void
	 */
	public function setAttachments(\TYPO3\Flow\Resource\Resource $attachments) {
		$this->attachments = $attachments;
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
	 * @param boolean $isSent
	 * @return void
	 */
	public function setIsSent($isSent) {
		$this->isSent = $isSent;
	}

	/**
	 * Gets ContentNode
	 *
	 * @return string contentNode
	 */
	public function getContentNode() {
		return $this->contentNode;
	}

	/**
	 * Sets ContentNode
	 *
	 * @param string contentNode
	 * @return void
	 */
	public function setContentNode($contentNode) {
		$this->contentNode = $contentNode;
	}

	/**
	 * Gets Categories
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getCategories() {
		return $this->categories;
	}

	/**
	 * Sets Categories
	 *
	 * @param \Doctrine\Common\Collections\Collection $categories
	 * @return void
	 */
	public function setCategories(\Doctrine\Common\Collections\Collection $categories) {
		$this->categories = $categories;
	}

	/**
	 * Gets RecipientGroups
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getRecipientGroups() {
		return $this->recipientGroups;
	}

	/**
	 * Sets RecipientGroups
	 *
	 * @param \Doctrine\Common\Collections\Collection $recipientGroups
	 * @return void
	 */
	public function setRecipientGroups(\Doctrine\Common\Collections\Collection $recipientGroups) {
		$this->recipientGroups = $recipientGroups;
	}

	/**
	 * Gets Recipients
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getRecipients() {
		return $this->recipients;
	}

	/**
	 * Sets Recipients
	 *
	 * @param \Doctrine\Common\Collections\Collection $recipients
	 * @return void
	 */
	public function setRecipients(\Doctrine\Common\Collections\Collection $recipients) {
		$this->recipients = $recipients;
	}

	/**
	 * Removes a Newsletters's Recipients
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person $person
	 * @return void
	 */
	public function removeRecipients(\Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person $person) {
		$this->recipients->removeElement($person);
	}

	/**
	 * Removes a Newsletters's Groups
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\AbstractGroup $recipientGroup Recipient Group
	 * @return void
	 */
	public function removeRecipientGroups(\Lelesys\Plugin\Newsletter\Domain\Model\Recipient\AbstractGroup $recipientGroup) {
		$this->recipientGroups->removeElement($recipientGroup);
	}

	/**
	 * Removes a Newsletters Category
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\AbstractGroup $recipientGroup Recipient Group
	 * @return void
	 */
	public function removeCategories(\Lelesys\Plugin\Newsletter\Domain\Model\Category $category) {
		$this->categories->removeElement($category);
	}

}

?>