<?php

namespace Test\Novelist\Document;

use KBCrafted\Novelist\Document\Element;
use KBCrafted\Novelist\Document\Exceptions\InvalidTokenException;
use KBCrafted\Novelist\Document\Parser\Lexer;
use KBCrafted\Novelist\Document\PathQuery;
use KBCrafted\Novelist\Document\Stream\FileInputStream;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PathQueryTest extends TestCase
{
    /**
     * @return void
     * @throws InvalidTokenException
     */
    #[Test]
    public function searchPath()
    {
        $filepath = TEST_ROOT_DIR . '/novelist.ndl';
        $inputStream = new FileInputStream($filepath);
        $lexer = new Lexer($inputStream);
        $element = new Element();
        $element->parse($lexer);

        $pathQuery = new PathQuery($element);
        $namespaces = $pathQuery->query('/novelist/application/namespaces/*');

        $this->assertCount(4, $namespaces);

        $this->assertEquals('api_controller', $namespaces[3]->getIdentifier());
    }
}