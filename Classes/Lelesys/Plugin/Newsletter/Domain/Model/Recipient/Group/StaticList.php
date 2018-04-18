<?php
namespace Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Group;

/*
 * This script belongs to the package "Lelesys.Plugin.Newsletter".         *
 *                                                                         *
 * It is free software; you can redistribute it and/or modify it under     *
 * the terms of the GNU Lesser General Public License, either version 3    *
 * of the License, or (at your option) any later version.                  *
 *                                                                         */

use Neos\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * StaticList
 *
 * @Flow\Entity
 */
class StaticList extends \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\AbstractGroup {

	/**
	 * RecipientList
	 *
	 * @var string
	 */
	protected $recipientList;

	/**
	 * Gets recipients
	 *
	 * @return string
	 */
	public function getRecipients() {
		return $this->recipientList;
	}

	/**
	 * Gets recipients
	 *
	 * @return string
	 */
	public function getRecipientList() {
		return $this->recipientList;
	}

	/**
	 * Sets recipients
	 *
	 * @param string $recipientList Recipients
	 * @return void
	 */
	public function setRecipientList($recipientList) {
		$this->recipientList = $recipientList;
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