<?php
namespace Lelesys\Plugin\Newsletter\ViewHelpers;

/* *
 * This script belongs to the Flow package "Lelesys.Plugin.Newsletter". *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * A view helper for sorting query result
 * @Flow\scope("prototype")
 */
class BackendSortViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @var \TYPO3\Flow\Configuration\ConfigurationManager
	 * @Flow\Inject
	 */
	protected $configurationManager;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Persistence\PersistenceManagerInterface
	 */
	protected $persistenceManager;

	/**
	 * @var \Doctrine\ORM\QueryBuilder
	 */
	protected $queryBuilder;

	/**
	 * @var boolean
	 */
	protected $escapeOutput = FALSE;

	/**
	 *
	 * @param mixed $objects
	 * @param string $as
	 * @param string $sortingAs
	 * @return string Rendered string
	 * @api
	 */
	public function render($objects = NULL, $as = 'sortedObjects', $sortingAs = 'sorting') {
		if ($objects != NULL) {
			$this->objects = $objects;
			$this->query = $objects->getQuery();

			$this->request = $this->controllerContext->getRequest();

			$sorting = array();

			if ($this->request->hasArgument('sort')) {
				$property = $this->request->getArgument('sort');
				if ($this->request->hasArgument('direction')) {
					$direction = $this->request->getArgument('direction');
				} else {
					$direction = 'DESC';
				}

				$this->query->setOrderings(array(
					$property => $direction
				));

				$sorting = array(
					'property' => $property,
					'direction' => $direction,
					'oppositeDirection' => $direction == 'ASC' ? 'DESC' : 'ASC'
				);
				$result = $this->query->execute();
			} else {
				$result = $this->query->execute();
			}

			$this->templateVariableContainer->add($sortingAs, $sorting);
			$this->templateVariableContainer->add($as, $result);
			$content = $this->renderChildren();
			$this->templateVariableContainer->remove($sortingAs);
			$this->templateVariableContainer->remove($as);

			return $content;
		}
	}

	public function sortResult($result) {
		$type = 'ASC';
		$allresult = array();

		foreach ($result as $k => $userarray) {
			$identifier = $this->persistenceManager->getIdentifierByObject($userarray);
			$name = $userarray->getParty()->getName();
			$newarray[$identifier] = $name;
			$oldarray[$identifier] = $userarray;
		}
		asort($newarray);
		foreach ($newarray as $k => $v) {
			$allresult[] = $oldarray[$k];
		}
		return $allresult;
	}

}

?>
