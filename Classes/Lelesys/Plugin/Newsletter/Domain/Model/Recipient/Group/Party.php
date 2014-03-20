<?php
namespace Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Group;

/*
 * This script belongs to the package "Lelesys.Plugin.Newsletter".         *
 *                                                                         *
 * It is free software; you can redistribute it and/or modify it under     *
 * the terms of the GNU Lesser General Public License, either version 3    *
 * of the License, or (at your option) any later version.                  *
 *                                                                         */

use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * Party
 *
 * @Flow\Entity
 */
class Party extends \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\AbstractGroup {

	/**
	 * Recipients
	 *
	 * @var \Doctrine\Common\Collections\Collection<\Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Person>
	 * @ORM\ManyToMany(mappedBy="groups")
	 */
	protected $recipients;

	/**
	 * Gets recipients
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getRecipients() {
		return $this->recipients;
	}

	/**
	 * Sets recipients
	 *
	 * @param \Doctrine\Common\Collections\Collection $recipients Recipients
	 * @return void
	 */
	public function setRecipients(\Doctrine\Common\Collections\Collection $recipients) {
		$this->recipients = $recipients;
	}

	/**
	 * Returns uuid of this object
	 *
	 * @return string
	 */
	public function getUuid() {
		return $this->Persistence_Object_Identifier;
	}

}
?>