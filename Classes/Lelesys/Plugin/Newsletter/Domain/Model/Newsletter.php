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

	const STATUS_ACTIVE = 1;
	const STATUS_INACTIVE = 2;

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
	 * The status
	 *
	 * @var integer
	 */
	protected $status;

	/**
	 * Attachments
	 *
	 * @var \Doctrine\Common\Collections\Collection<\TYPO3\Media\Domain\Model\Document>
	 * @ORM\ManyToMany(cascade={"detach","refresh","remove"})
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
		$this->status = TRUE;
		$this->recipients = new \Doctrine\Common\Collections\ArrayCollection();
		$this->recipientGroups = new \Doctrine\Common\Collections\ArrayCollection();
		$this->categories = new \Doctrine\Common\Collections\ArrayCollection();
		$this->attachments = new \Doctrine\Common\Collections\ArrayCollection();
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
	 * @param string $fromName FromName
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
	 * @param string $fromEmail FromEmail
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
	 * @param string $replyToName ReplyToName
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
	 * @param string $replyToEmail ReplyToEmail
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
	 * @param string $organisation Organisation
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
	 * @param string $subject Subject
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
	 * @param string $encoding Encoding
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
	 * @param string $characterSet CharacterSet
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
	 * @param string $htmlBody HtmlBody
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
	 * @param string $plainTextBody PlainTextBody
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
	 * @param integer $priority Priority
	 * @return void
	 */
	public function setPriority($priority) {
		$this->priority = $priority;
	}

	/**
	 * Gets Attachments
	 *
	 * @return \Doctrine\Common\Collections\Collection The Newsletter's attachments
	 */
	public function getAttachments() {
		return $this->attachments;
	}

	/**
	 * Sets Attachments
	 *
	 * @param \Doctrine\Common\Collections\Collection $attachments Attachments
	 * @return void
	 */
	public function setAttachments(\Doctrine\Common\Collections\Collection $attachments) {
		$this->attachments = $attachments;
	}

	/**
	 * Adds Attachments
	 *
	 * @param \TYPO3\Media\Domain\Model\Document $attachment Attachments
	 * @return void
	 */
	public function addAttachment(\TYPO3\Media\Domain\Model\Document $attachment) {
		$this->attachments->add($attachment);
	}

	/**
	 * Removes Attachments
	 *
	 * @param \TYPO3\Media\Domain\Model\Document $attachment Attachments
	 * @return void
	 */
	public function removeAttachment(\TYPO3\Media\Domain\Model\Document $attachment) {
		$this->attachments->removeElement($attachment);
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
	 * @param \Doctrine\Common\Collections\Collection $categories Categories
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
	 * @param \Doctrine\Common\Collections\Collection $recipientGroups Recipient groups
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
	 * @param \Doctrine\Common\Collections\Collection $recipients Recipients
	 * @return void
	 */
	public function setRecipients(\Doctrine\Common\Collections\Collection $recipients) {
		$this->recipients = $recipients;
	}

	/**
	 * Removes a Newsletters's Recipients
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person $person Person object
	 * @return void
	 */
	public function removeRecipient(\Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person $person) {
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

	/**
	 * Get the Newsletters status
	 *
	 * @return integer TheNewsletters status
	 */
	public function getStatus() {
		return $this->status;
	}

	/**
	 * Sets this Newsletters status
	 *
	 * @param integer $status The Newsletters status
	 * @return void
	 */
	public function setStatus($status) {
		$this->status = $status;
	}

	/**
	 * Get Persistence_Object_Identifier of Newsletter
	 *
	 * @return string
	 */
	public function getIdentifier() {
		return $this->Persistence_Object_Identifier;
	}
}
?>