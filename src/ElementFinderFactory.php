<?php

  namespace Xparse\Parser;

  use GuzzleHttp\Psr7\Response;
  use Xparse\ElementFinder\ElementFinder;
  use Xparse\ElementFinder\Helper;
  use Xparse\Parser\Helper\HtmlEncodingConverter;
  use Xparse\Parser\Helper\LinkConverter;

  /**
   * Create ElementFinder.
   * Convert charset to UTF-8
   * Convert relative links to absolute
   * @package Xparse\Parser\Helper
   */
  class ElementFinderFactory implements ElementFinderFactoryInterface {

    /**
     * @inheritdoc
     */
    public function create(Response $response, $affectedUrl = null) {
      $html = $response->getBody();
      $html = Helper::safeEncodeStr((string) $html);
      $contentType = $response->getHeaderLine('content-type');
      $html = HtmlEncodingConverter::convertToUtf($html, $contentType);
      $page = new ElementFinder((string) $html);

      if ($affectedUrl !== null) {
        LinkConverter::convertUrlsToAbsolute($page, $affectedUrl);
      }

      return $page;
    }

  }