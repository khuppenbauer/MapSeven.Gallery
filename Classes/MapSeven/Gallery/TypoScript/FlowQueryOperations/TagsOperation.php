<?php
namespace MapSeven\Gallery\TypoScript\FlowQueryOperations;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "MapSeven.Gallery".      *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License as published by the Free   *
 * Software Foundation, either version 3 of the License, or (at your      *
 * option) any later version.                                             *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Eel\FlowQuery\FlowQuery;
use TYPO3\Eel\FlowQuery\Operations\AbstractOperation;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Utility\Arrays;
use TYPO3\Media\Domain\Repository\TagRepository;
use TYPO3\Media\Domain\Repository\AssetRepository;

/**
 * FlowQuery that return the assets with the given tag(s)
 */
class TagsOperation extends AbstractOperation {

	const PROPERTYPATH = 'nodeData.properties';

	/**
	 * {@inheritdoc}
	 *
	 * @var string
	 */
	static protected $shortName = 'tags';

	/**
	 * {@inheritdoc}
	 *
	 * @var integer
	 */
	static protected $priority = 100;

	/**
	 * {@inheritdoc}
	 *
	 * @param array (or array-like object) $context onto which this operation should be applied
	 * @return boolean TRUE if the operation can be applied onto the $context, FALSE otherwise
	 */
	public function canEvaluate($context) {
		return TRUE;
	}

	/**
	 * @var string
	 */
	protected $settings;

	/**
	 * @Flow\Inject
	 * @var AssetRepository
	 */
	protected $assetRepository;

	/**
	 * @Flow\Inject
	 * @var TagRepository
	 */
	protected $tagRepository;

	/**
	 * @param array $settings
	 */
	public function injectSettings(array $settings) {
		$this->settings = $settings;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param FlowQuery $flowQuery the FlowQuery object
	 * @param array $arguments A mandatory start and optional end index in the context, negative indices indicate an offset from the start or end respectively
	 * @return void
	 */
	public function evaluate(FlowQuery $flowQuery, array $arguments) {
		$context = $flowQuery->getContext();
		if (!isset($context[0])) {
			return NULL;
		}

		$element = $context[0];
		$properties = \TYPO3\Flow\Reflection\ObjectAccess::getPropertyPath($element, self::PROPERTYPATH);
		$tagArray = array();
		$output = array();
		if (isset($properties['tags'])) {
			foreach ($properties['tags'] as $tag) {
				$tagArray[] = $this->tagRepository->findOneByLabel($tag);
			}
			$assets = $this->assetRepository->findBySearchTermOrTags('*', $tagArray)->toArray();
		}
		if (isset($properties['limit'])) {
			$assets = array_slice($assets, 0, $properties['limit']);
		}
		if (count($assets) > 0) {
			$previewImage = Arrays::getValueByPath($properties, 'previewImage');
			if (!empty($previewImage)) {
				if (is_string($previewImage)) {
					$previewImage = $this->assetRepository->findByIdentifier($previewImage);
				}
				if ($previewImage instanceof \TYPO3\Media\Domain\Model\ImageVariant) {
					$originalPreviewImage = $previewImage->getOriginalAsset();					
				} else {
					$originalPreviewImage = $previewImage;										
				}
				$i = 2;
				foreach ($assets as $asset) {
					if ($asset->getIdentifier() !== $originalPreviewImage->getIdentifier()) {
						$tempAssets[$i] = $asset;
						$i++;
					}
				}
				$assets = !empty($tempAssets) ? $tempAssets : array();
				$count = count($assets) + 1;
			} else {
				$count = count($assets);
			}
			$output['count'] = $count;
			$output['assets'] = $assets;
		}
		$flowQuery->setContext($output);
	}
}