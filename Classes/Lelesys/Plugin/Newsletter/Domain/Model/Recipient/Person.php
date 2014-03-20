<?php
namespace Lelesys\Plugin\Newsletter\Domain\Model\Recipient;

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
 * Person
 *
 * @Flow\Entity
 */
class Person extends \TYPO3\Party\Domain\Model\Person {

	/**
	 * Gender
	 *
	 * @var boolean
	 * @Flow\Validate(type="NotEmpty")
	 */
	protected $gender;

	/**
	 * AcceptsHtml
	 *
	 * @var boolean
	 * @ORM\Column(nullable=true)
	 */
	protected $acceptsHtml;

	/**
	 * Recipient Groups
	 *
	 * @var \Doctrine\Common\Collections\Collection<\Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Group\Party>
	 * @ORM\ManyToMany(cascade={"persist", "detach"}, inversedBy="recipients")
	 */
	protected $groups;

	/**
	 * Categories
	 *
	 * @var \Doctrine\Common\Collections\Collection<\Lelesys\Plugin\Newsletter\Domain\Model\Category>
	 * @ORM\ManyToMany(cascade={"persist", "detach"})
	 * @ORM\Column(nullable=true)
	 */
	protected $categories;

	/**
	 * Constructor
	 *
	 */
	public function __construct() {
		parent::__construct();
		$this->categories = new \Doctrine\Common\Collections\ArrayCollection();
	}

	/**
	 * Gets gender
	 *
	 * @return boolean
	 */
	public function getGender() {
		return $this->gender;
	}

	/**
	 * Sets gender
	 *
	 * @param boolean $gender
	 * @return void
	 */
	public function setGender($gender) {
		$this->gender = $gender;
	}

	/**
	 * Gets Accepts Html
	 *
	 * @return boolean
	 */
	public function getAcceptsHtml() {
		return $this->acceptsHtml;
	}

	/**
	 * Sets Accepts Html
	 *
	 * @param boolean $acceptsHtml
	 * @return void
	 */
	public function setAcceptsHtml($acceptsHtml) {
		$this->acceptsHtml = $acceptsHtml;
	}

	/**
	 * Gets groups
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getGroups() {
		return $this->groups;
	}

	/**
	 * Sets groups
	 *
	 * @param \Doctrine\Common\Collections\Collection $groups
	 * @return void
	 */
	public function setGroups(\Doctrine\Common\Collections\Collection $groups) {
		$this->groups = $groups;
	}

	/**
	 * Get Categories
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
	 * Returns uuid of this object
	 *
	 * @return string
	 */
	public function getUuid() {
		return $this->Persistence_Object_Identifier;
	}

	/**
	 * Removes a Recipients Category
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Category $category Recipient Category
	 * @return void
	 */
	public function removeCategories(\Lelesys\Plugin\Newsletter\Domain\Model\Category $category) {
		$this->categories->removeElement($category);
	}

}
?>