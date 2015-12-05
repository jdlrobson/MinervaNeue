<?php
/**
 * Page.php
 */
namespace models;
use Title;
use PageImages;
use Revision;
use User;

/**
 * Retrieves information specific to a mobile page
 * Currently this only provides helper functions for loading PageImage associated with a page
 * @todo FIXME: Rename when this class when its purpose becomes clearer
 */
class Page {
	const SMALL_IMAGE_WIDTH = 150;
	const TINY_IMAGE_WIDTH = 80;

	/**
	 * @var Title: Title for page
	 */
	private $title;
	/**
	 * @var File Associated page image file (see PageImages extension)
	 */
	private $file;
	/**
	 * @var string Page content
	 */
	private $content;
	/**
	 * @var boolean Whether to use page images
	 */
	private $usePageImages;

	/**
	 * Constructor
	 * @param Title $title
	 * @param File|bool $file
	 */
	public function __construct( Title $title, $file = false ) {
		$this->title = $title;
		// @todo FIXME: check existence
		if ( defined( 'PAGE_IMAGES_INSTALLED' ) ) {
			$this->usePageImages = true;
			$this->file = $file ? $file : PageImages::getPageImage( $title );
		}
	}

	/**
	 * Retrieve the last time the page content was modified. Do not reflect null edits.
	 * @return string timestamp representing last edited time.
	 */
	public function getLatestTimestamp() {
		$title = $this->getTitle();
		return Revision::getTimestampFromId( $title, $title->getLatestRevID() );
	}

	/**
	 * Retrieve the last edit to this page.
	 * @return array defining edit with keys name, timestamp and gender
	 */
	public function getLatestEdit() {
		$rev = Revision::newFromId( $this->getTitle()->getLatestRevID() );
		$unixTimestamp = wfTimestamp( TS_UNIX, $this->getLatestTimestamp() );
		$edit = array(
			'timestamp' => $unixTimestamp,
			'name' => '',
			'gender' => '',
		);
		if ( $rev ) {
			$userId = $rev->getUser();
			if ( $userId ) {
				$revUser = User::newFromId( $userId );
				$revUser->load( User::READ_NORMAL );
				$edit['name'] = $revUser->getName();
				$edit['gender'] = $revUser->getOption( 'gender' );
			}
		}
		return $edit;
	}

	/**
	 * Get the title of the page
	 *
	 * @return Title
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * Get a placeholder div container for thumbnails
	 * @param string $className
	 * @param string $iconClassName controls size of thumbnail, defaults to icon-32px
	 * @return string
	 */
	public static function getPlaceHolderThumbnailHtml( $className, $iconClassName = 'icon-32px' ) {
		return Html::element( 'div', array(
			'class' => 'list-thumb list-thumb-placeholder ' . $iconClassName . ' ' . $className,
		) );
	}

	/**
	 * Check whether a page has a thumbnail associated with it
	 *
	 * @return Boolean whether the page has an image associated with it
	 */
	public function hasThumbnail() {
		return $this->file ? true : false;
	}

	/**
	 * Get a small sized thumbnail in div container.
	 *
	 * @param boolean $useBackgroundImage Whether the thumbnail should have a background image
	 * @return string
	 */
	public function getSmallThumbnailHtml( $useBackgroundImage = false ) {
		return $this->getPageImageHtml( self::SMALL_IMAGE_WIDTH, $useBackgroundImage );
	}

	/**
	 * Get the thumbnail container for getMediumThumbnailHtml() and getSmallThumbnailHtml().
	 *
	 * @param integer $size the width of the thumbnail
	 * @param boolean $useBackgroundImage Whether the thumbnail should have a background image
	 * @return string
	 */
	private function getPageImageHtml( $size, $useBackgroundImage = false ) {
		$imageHtml = '';
		// FIXME: Use more generic classes - no longer restricted to lists
		if ( $this->usePageImages ) {
			$file = $this->file;
			if ( $file ) {
				$thumb = $file->transform( array( 'width' => $size ) );
				if ( $thumb && $thumb->getUrl() ) {
					$className = 'list-thumb ';
					$className .= $thumb->getWidth() > $thumb->getHeight()
						? 'list-thumb-y'
						: 'list-thumb-x';
					$props = array(
						'class' => $className,
					);

					$imgUrl = wfExpandUrl( $thumb->getUrl(), PROTO_CURRENT );
					if ( $useBackgroundImage ) {
						$props['style'] = 'background-image: url("' . wfExpandUrl( $imgUrl, PROTO_CURRENT ) . '")';
						$text = '';
					} else {
						$props['src'] = $imgUrl;
						$text = $this->title->getText();
					}
					$imageHtml = Html::element( $useBackgroundImage ? 'div' : 'img', $props, $text );
				}
			}
		}
		return $imageHtml;
	}
}
