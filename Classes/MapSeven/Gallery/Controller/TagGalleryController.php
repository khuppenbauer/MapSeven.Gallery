<?php
namespace MapSeven\Gallery\Controller;

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

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Mvc\Controller\ActionController;
use TYPO3\Flow\Utility\Arrays;
use TYPO3\Media\Domain\Model\Asset;
use TYPO3\Media\Domain\Model\Tag;
use TYPO3\Media\Domain\Repository\AssetRepository;
use TYPO3\Media\Domain\Repository\TagRepository;

/**
 * Controller that displays the assets with the given tag(s)
 */
class TagGalleryController extends ActionController {

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
	 * @return void
	 */
	public function indexAction() {
		$node = $this->request->getInternalArgument('__node');
		if ($node === NULL) {
			return;
		}
		$properties = $node->getProperties();
		$tagArray = array();
		if (isset($properties['tags'])) {
			foreach ($properties['tags'] as $tag) {
				$tagArray[] = $this->tagRepository->findOneByLabel($tag);
			}
			$assets = $this->assetRepository->findBySearchTermOrTags('*', $tagArray)->toArray();
			if (count($assets) > 0) {
				$maximumWidth = !empty($properties['width']) ? $properties['width'] : $this->settings['maximumWidth'];
				$maximumHeight = !empty($properties['height']) ? $properties['height'] : $this->settings['maximumHeight'];
				/** @var Asset $previewImage */
				$previewImage = !empty($properties['previewImage']) ? $properties['previewImage'] : NULL;
				if ($previewImage !== NULL) {
					$originalPreviewÍmage = $previewImage->getOriginalImage();
					$i = 2;
					foreach ($assets as $key => $asset) {
						if ($asset->getIdentifier() !== $originalPreviewÍmage->getIdentifier()) {
							$tempAssets[$i] = $asset;
							$i++;
						}
					}
					$assets = !empty($tempAssets) ? $tempAssets : array();
					$count = count($assets) + 1;
				} else {
					$count = count($assets);
				}
				$this->view->assign('previewImage', $previewImage);
				$this->view->assign('assets', $assets);
				$this->view->assign('gallery', $node->getIdentifier());
				$this->view->assign('count', $count);
				$this->view->assign('maximumWidth', $maximumWidth);
				$this->view->assign('maximumHeight', $maximumHeight);
				$this->view->assign('properties', $properties);
			}
		}
	}

}
