<?php
namespace MapSeven\Gallery\DataSource;

use Neos\Flow\Annotations as Flow;
use Neos\Neos\Service\DataSource\AbstractDataSource;
use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\Media\Domain\Repository\TagRepository;
use Neos\Media\Domain\Repository\AssetRepository;

/**
 * Class TagDataSource
 *
 * @package MapSeven\Gallery\DataSource
 */
class TagDataSource extends AbstractDataSource {

	/**
	 * @var string
	 */
	static protected $identifier = 'mapseven-gallery-tag';

	/**
	 * @Flow\Inject
	 * @var TagRepository
	 */
	protected $tagRepository;

	/**
	 * @Flow\Inject
	 * @var AssetRepository
	 */
	protected $assetRepository;

	/**
	 * Get data
	 *
	 * @param NodeInterface $node The node that is currently edited (optional)
	 * @param array $arguments Additional arguments (key / value)
	 * @return array JSON serializable data
	 */
	public function getData(NodeInterface $node = NULL, array $arguments) {
		$tags = $this->tagRepository->findAll();
		$tagsArray = array();
		foreach ($tags as $tag) {
			$count = $this->assetRepository->countByTag($tag);
			$label = $tag->getLabel();
			$tagsArray[$label]['label'] = $label . ' (' . $count . ')';
			$tagsArray[$label]['icon'] = 'icon-tag';
		}
		return $tagsArray;
	}

}