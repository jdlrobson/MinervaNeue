<?php
/**
 * MinervaNeueTemplate.php
 */

/**
 * Extended Template class of BaseTemplate for mobile devices
 */
class MinervaNeueTemplate extends BaseTemplate {
	/** @var boolean Specify whether the page is a special page */
	protected $isSpecialPage;

	/** @var boolean Whether or not the user is on the Special:MobileMenu page */
	protected $isSpecialMobileMenuPage;

	/** @var boolean Specify whether the page is main page */
	protected $isMainPage;

	/**
	 * Generates the HTML required to render the search form.
	 *
	 * @param array $data The data used to render the page
	 * @return string
	 */
	protected function getSearchForm( $data ) {
		return Html::openElement( 'form',
				array(
					'action' => $data['wgScript'],
					'class' => 'search-box',
				)
			) .
			$this->makeSearchInput( $this->getSearchAttributes() ) .
			$this->makeSearchButton(
				'fulltext',
				array(
					'class' => MobileUI::buttonClass( 'progressive', 'fulltext-search no-js-only' ),
				)
			) .
			Html::closeElement( 'form' );
	}

	/**
	 * Start render the page in template
	 */
	public function execute() {
		$title = $this->getSkin()->getTitle();
		$this->isSpecialPage = $title->isSpecialPage();
		$this->isSpecialMobileMenuPage = $this->isSpecialPage &&
			$title->equals( SpecialPage::getTitleFor( 'MobileMenu' ) );
		$this->isMainPage = $title->isMainPage();
		$this->render( $this->data );
	}

	/**
	 * Returns available page actions
	 * @return array
	 */
	public function getPageActions() {
		return $this->data['page_actions'];
	}

	/**
	 * Get attributes to create search input
	 * @return array Array with attributes for search bar
	 */
	protected function getSearchAttributes() {
		$searchBox = array(
			'id' => 'searchInput',
			'class' => 'search',
			'autocomplete' => 'off',
			// The placeholder gets fed to HTML::element later which escapes all
			// attribute values, so need to escape the string here.
			'placeholder' => '',
		);
		return $searchBox;
	}

	/**
	 * Render Footer elements
	 * @param array $data Data used to build the footer
	 * @return array
	 */
	protected function getFooterRows( $data ) {
		$rows = array();
		foreach ( $this->getFooterLinks() as $category => $links ) {
			$cols = array();
			foreach ( $links as $link ) {
				if ( isset( $this->data[$link] ) && $this->data[$link] !== '' ) {
					$cols[] = array(
						'category' => $category,
						'link' => $link,
						'linkhtml' => $this->data[$link],
					);
				}
			}
			$rows[] = array(
				'category' => $category,
				'columns' => $cols,
			);
		}
		return $rows;
	}

	/**
	 * Render available page actions
	 * @param array $data Data used to build page actions
	 */
	protected function getPageActionsHtml( $data ) {
		$actions = $this->getPageActions();
		$html = '';
		if ( $actions ) {
			foreach ( $actions as $key => $val ) {
				$html .= $this->makeListItem( $key, $val );
			}
		}
		return $html;
	}

	/**
	 * Get page secondary actions
	 */
	protected function getSecondaryActions() {
		$result = $this->data['secondary_actions'];
		$hasLanguages = $this->data['content_navigation']['variants'] ||
			$this->data['language_urls'];

		// If languages are available, add a languages link
		if ( $hasLanguages ) {
			$languageUrl = SpecialPage::getTitleFor(
				'MobileLanguages',
				$this->getSkin()->getTitle()
			)->getLocalURL();

			$result['language'] = array(
				'attributes' => array(
					'class' => 'languageSelector',
					'href' => $languageUrl,
				),
				'label' => wfMessage( 'mobile-frontend-language-article-heading' )->text()
			);
		}

		return $result;
	}

	/**
	 * Get HTML representing secondary page actions like language selector
	 * @return string
	 */
	protected function getSecondaryActionsHtml() {
		$baseClass = MobileUI::buttonClass( '', 'button' );
		$html = Html::openElement( 'div', array(
			'class' => 'post-content',
			'id' => 'page-secondary-actions'
		) );

		foreach ( $this->getSecondaryActions() as $el ) {
			if ( isset( $el['attributes']['class'] ) ) {
				$el['attributes']['class'] .= ' ' . $baseClass;
			} else {
				$el['attributes']['class'] = $baseClass;
			}
			$html .= Html::element( 'a', $el['attributes'], $el['label'] );
		}

		return $html . Html::closeElement( 'div' );
	}

	/**
	 * Render the entire page
	 * @param array $data Data used to build the page
	 * @todo replace with template engines
	 */
	protected function render( $data ) {
		$templateParser = new TemplateParser( __DIR__ );
		$internalBanner = $data[ 'internalBanner' ];
		$preBodyText = isset( $data['prebodyhtml'] ) ? $data['prebodyhtml'] : '';
		$data = array_merge( $data, array(
			// boolean flags
			'hasHistory' => isset( $data['historyLink'] ),
			'hasSubjectPage' => isset( $data['subject-page'] ),
			'isSpecialPage' => $this->isSpecialPage,
			'hasPageActions' => count( $this->getPageActions() ),
			'hasPreBodyText' => $internalBanner || $preBodyText || isset( $data['page_actions'] ),
			// data structures
			'footerRows' => $this->getFooterRows( $data ),
			// html
			'debug' => MWDebug::getDebugHTML( $this->getSkin()->getContext() ),
			'pageactionshtml' => $this->getPageActionsHtml( $data ),
			'secondaryactionshtml' => $this->getSecondaryActionsHtml(),
			'searchhtml' => $this->getSearchForm( $data ),
		) );

		echo $templateParser->processTemplate( 'minervaNeue', $data );
	}
}
