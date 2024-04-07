<?php

namespace KBCrafted\Novelist\Document;

/**
 * A utility class for querying elements within a document structure based on a specified path.
 *
 * PathQuery is inspired by XPath but does not support all of it's features.
 *
 * PathQuery support the following features:
 *
 * | --------------- | ----------- | ------------------------------------------------------------ |
 * | Feature         | Example     | Description                                                  |
 * | --------------- | ----------- | ------------------------------------------------------------ |
 * | Node expression | model       | Selects all nodes with the name "model"                      |
 * | Root expression | /           | Selects from the root node                                   |
 * | Node wildcard   | *           | Matches any element node                                     |
 * | Path expression | bookstore/* | Selects all the child nodes of the "bookstore" element       |
 * | --------------- | ----------- | ------------------------------------------------------------ |
 */
class PathQuery
{
    /**
     * The root element of the document structure.
     * @var Element
     */
    protected Element $element;

    /**
     * The index of path queries mapped to element identifiers.
     * @var array
     */
    protected array $pathQueryIndex;

    /**
     * Constructs a new PathQuery instance with the specified root element.
     *
     * @param Element $element The root element of the document structure.
     */
    public function __construct(Element $element)
    {
        $this->element = $element;
        $this->pathQueryIndex = $this->generatePathQueryIndex($element);
    }

    /**
     * Searches for an element within the document structure based on the specified path query.
     *
     * @param string $path The path query to search for the element.
     * @return Element[]
     */
    public function query(string $path): array
    {
        $matches = [];

        $patternReplacements = [
            '~(\w+)~' => '($1)',
            '~^/~'    => '^/',
            '~\*~'    => '\w+'
        ];

        $pattern = preg_replace(
            array_keys($patternReplacements),
            array_values($patternReplacements),
            $path);

        $pattern = "~$pattern$~";

        foreach ($this->pathQueryIndex as $pathQueryIndex => $pathValue) {
            if(preg_match($pattern, $pathQueryIndex)) {
                $matches[] = $pathValue;
            }
        }
        return $matches;
    }


    /**
     * Generates an associative array representing the index of path queries for the given element.
     *
     * @param Element $element The element to generate path queries for.
     * @param string $parentIdentifier The identifier of the parent element, if any.
     * @param int $childIndex The index of the child element within its parent.
     * @return array An associative array of path queries to element identifiers.
     */
    protected function generatePathQueryIndex(Element $element, string $parentIdentifier = '', int $childIndex = 0): array
    {
        $pathQueries = [];

        $pathQueryIdentifier = $this->composePathQueryIdentifier($element, $parentIdentifier, $childIndex);

        $pathQueries[$pathQueryIdentifier] = $element;

        $childIndex = 0;
        foreach ($element->getChildren() as $child) {
            $childPathQueries = $this->generatePathQueryIndex($child, $pathQueryIdentifier, $childIndex);
            $pathQueries = array_merge($pathQueries, $childPathQueries);
            $childIndex++;
        }

        return $pathQueries;
    }

    /**
     * Composes a path query identifier for the given element, incorporating parent and child information.
     *
     * @param Element $element The current element.
     * @param string $parentIdentifier The identifier of the parent element.
     * @param int $childIndex The index of the child element within its parent.
     * @return string The composed path query identifier.
     */
    protected function composePathQueryIdentifier(Element $element, string $parentIdentifier = '', int $childIndex = 0): string
    {
        $identifier = empty($element->getIdentifier()) ? $childIndex : $element->getIdentifier();
        return implode('/', [$parentIdentifier, $identifier]);
    }
}