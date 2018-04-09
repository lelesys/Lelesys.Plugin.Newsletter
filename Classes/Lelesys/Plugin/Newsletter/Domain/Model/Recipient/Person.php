<?php
namespace Lelesys\Plugin\Newsletter\Domain\Model\Recipient;

/* *
 * This script belongs to the package "Lelesys.Plugin.Newsletter".         *
 *                                                                         *
 * It is free software; you can redistribute it and/or modify it under     *
 * the terms of the GNU Lesser General Public License, either version 3    *
 * of the License, or (at your option) any later version.                  *
 *                                                                         */

use Neos\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * Person
 *
 * @Flow\Entity
 */
class Person extends \Neos\Party\Domain\Model\Person {

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
	protected $newsletterCategories;

	/**
	 * Subscribed to newsletter.
	 *
	 * @var boolean
	 * @ORM\Column(nullable=true)
	 */
	protected $subscribedToNewsletter;

	/**
	 * Constructor
	 *
	 */
	public function __construct() {
		parent::__construct();
		$this->newsletterCategories = new \Doctrine\Common\Collections\ArrayCollection();
		$this->groups = new \Doctrine\Common\Collections\ArrayCollection();
		$this->subscribedToNewsletter = FALSE;
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
	 * Get Newsletter Categories
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getNewsletterCategories() {
		return $this->newsletterCategories;
	}

	/**
	 * Sets Newsletter Categories
	 *
	 * @param \Doctrine\Common\Collections\Collection $newsletterCategories
	 * @return void
	 */
	public function setNewsletterCategories(\Doctrine\Common\Collections\Collection $newsletterCategories) {
		$this->newsletterCategories = $newsletterCategories;
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
	 * Removes a Recipients Newsletter Category
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Category $newsletterCategories Recipient Category
	 * @return void
	 */
	public function removeNewsletterCategories(\Lelesys\Plugin\Newsletter\Domain\Model\Category $newsletterCategories) {
		$this->newsletterCategories->removeElement($newsletterCategories);
	}

	/**
	 * Is person subscribed to newsletter
	 *
	 * @return boolean
	 */
	public function isSubscribedToNewsletter() {
		return $this->subscribedToNewsletter;
	}

	/**
	 * Set person subscribed to newsletter
	 *
	 * @param boolean $subscribedToNewsletter Subscribed to newsletter
	 * @return void
	 */
	public function setSubscribedToNewsletter($subscribedToNewsletter) {
		$this->subscribedToNewsletter = $subscribedToNewsletter;
	}

	/**
	 * Add group
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Group\Party $group
	 * @return void
	 */
	public function addGroup(\Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Group\Party $group) {
		$this->groups->add($group);
	}

	/**
	 * Remove group
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Group\Party $group
	 * @return void
	 */
	public function removeGroup(\Lelesys\Plugin\Newsletter\Domain\Model\Recipient\Group\Party $group) {
		$this->groups->removeElement($group);
	}

	/**
	 * Add Recipient Newsletter Category
	 *
	 * @param \Lelesys\Plugin\Newsletter\Domain\Model\Category $newsletterCategory Recipient Category
	 * @return void
	 */
	public function addNewsletterCategory(\Lelesys\Plugin\Newsletter\Domain\Model\Category $newsletterCategory) {
		$this->newsletterCategories->add($newsletterCategory);
	}
}
?>