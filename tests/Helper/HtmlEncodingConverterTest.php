<?php

  namespace Test\Xparse\Parser\Helper;

  use GuzzleHttp\Psr7\Response;
  use Xparse\ElementFinder\ElementFinder;
  use Xparse\Parser\Helper\HtmlEncodingConverter;

  /**
   * Class EncodingConverterTest
   * @package Test\Xparse\Parser\Helper
   */
  class HtmlEncodingConverterTest extends \PHPUnit_Framework_TestCase {

    /**
     * @return array
     */
    public function getDifferentCharsetStylesDataProvider() {
      return [
        [
          '<body></body>',
          '',
          ['content-type' => 'df'],
        ],
        [
          iconv('UTF-8', 'WINDOWS-1251', '<meta charset=\' windows-1251 \'><body>Текст текст text</body>'),
          'Текст текст text',
          ['content-type' => 'df'],
        ],
        [
          iconv('UTF-8', 'WINDOWS-1251', '<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" /><body>Текст текст text</body>'
          ),
          'Текст текст text',
          ['content-type' => 'text/html; charset=windows-1251'],
        ],
        [
          iconv('UTF-8', 'WINDOWS-1251', '<meta http-equiv="Content-Type" content=\'text/html; charset=windows-1251\' /><body>Текст текст text</body>'),
          'Текст текст text',
        ],
        [
          iconv('UTF-8', 'WINDOWS-1251', '<meta charset=\' windows-1251    \'><body>Текст текст text</body>'),
          'Текст текст text',
        ],
        [
          iconv('UTF-8', 'WINDOWS-1251', '<meta http-equiv="Content-Type" content="text/html; charset=test-as225" /><body>Текст текст text</body>'),
          '  text',
        ],
        [
          iconv('UTF-8', 'WINDOWS-1251', '<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" /><body>Текст текст text</body>'),
          'Текст текст text',
        ],
        [
          '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><body>Текст текст text</body>',
          'Текст текст text',
        ],
        [
          '<meta http-equiv="Content-Type" content="text/html; charset=utf8" /><body>Текст текст text</body>',
          'Текст текст text',
        ],
        [
          iconv('UTF-8', 'WINDOWS-1251', '<meta charset="windows-1251"><body>Текст текст text</body>'),
          'Текст текст text',
        ],
        [

          '<body></body>',
          '',
        ],
      ];
    }


    /**
     * @dataProvider getDifferentCharsetStylesDataProvider
     * @param array $headers
     * @param string $html
     * @param string $bodyText
     */
    public function testDifferentCharsetStyles($html, $bodyText, array $headers = []) {
      $response = new Response(200, $headers, $html);
      $contentType = $response->getHeaderLine('content-type');
      $html = HtmlEncodingConverter::convertToUtf($html, $contentType);
      $page = new ElementFinder((string) $html);
      $pageBodyText = $page->content('//body')->getFirst();
      self::assertInstanceOf(ElementFinder::class, $page);
      self::assertEquals($bodyText, $pageBodyText);
    }

  }
