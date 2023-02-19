<?php

declare(strict_types=1);

/*
 * This file is part of the Valkyrja Framework package.
 *
 * (c) Melech Mizrachi <melechmizrachi@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Valkyrja\Annotation\Parsers;

use JsonException;
use Valkyrja\Annotation\Annotation;
use Valkyrja\Annotation\Config\Config;
use Valkyrja\Annotation\Constants\Part;
use Valkyrja\Annotation\Constants\Regex;
use Valkyrja\Annotation\Exceptions\InvalidAnnotationKeyArgument;
use Valkyrja\Annotation\Models\Annotation as AnnotationModel;
use Valkyrja\Annotation\Parser as Contract;
use Valkyrja\Type\Support\Arr;

use function array_key_exists;
use function constant;
use function defined;
use function explode;
use function is_array;
use function is_string;
use function method_exists;
use function preg_match_all;
use function property_exists;
use function str_replace;
use function trim;

/**
 * Class Parser.
 *
 * @author Melech Mizrachi
 */
class Parser implements Contract
{
    /**
     * Parser constructor.
     */
    public function __construct(
        protected Config|array $config
    ) {
    }

    /**
     * @inheritDoc
     *
     * @throws InvalidAnnotationKeyArgument
     * @throws JsonException
     */
    public function getAnnotations(string $docString): array
    {
        $annotations = [];

        // Get all matches of @ Annotations
        $matches = $this->getMatches($docString);

        // If there are any matches iterate through them and create new
        // annotations
        if ($matches && isset($matches[0], $matches[1])) {
            foreach ($matches[0] as $index => $match) {
                $this->setAnnotation($matches, $index, $annotations);
            }
        }

        return $annotations;
    }

    /**
     * @inheritDoc
     *
     * @throws JsonException
     */
    public function getPropertiesAsArray(string $arguments = null): ?array
    {
        // If a valid properties list was passed in
        if ($arguments !== null) {
            $testArgs       = str_replace('=', ':', $arguments);
            $propertiesList = Arr::fromString('{' . $testArgs . '}');

            foreach ($propertiesList as &$value) {
                $value = $this->determinePropertyValue($value);
            }

            unset($value);

            return $propertiesList;
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function getRegex(): string
    {
        /*
         * @description
         *
         * This regex will produce an array matches list of
         * $matches[0] All matches
         * $matches[1] Annotation name
         *
         * @Annotation($data)
         *
         * $matches[2] Parenthesis enclosed results
         *
         * @Annotation $matches[3] $matches[4] $matches[5]
         * Matches: @method, @param $test, @description description,
         *          @param int $test Description, etc
         * None of the below matches requires the previous one to exist
         *
         * $matches[3] Description or type part of a line annotation
         * $matches[4] Variable part of line annotation
         *             (must be a variable beginning with $)
         * $matches[5] Description part of line annotation
         */
        return Regex::REGEX;
    }

    /**
     * @inheritDoc
     */
    public function getAnnotationsMap(): array
    {
        return $this->config['map'];
    }

    /**
     * @inheritDoc
     */
    public function getAnnotationFromMap(string $annotationType): Annotation
    {
        // Get the annotations map (annotation name to annotation class)
        $annotationsMap = $this->getAnnotationsMap();

        // If an annotation is mapped to a class
        if ($annotationType && array_key_exists($annotationType, $annotationsMap)) {
            // Set a new class based on the match found
            $annotation = new $annotationsMap[$annotationType]();
        } else {
            // Otherwise set a new base annotation model
            $annotation = new AnnotationModel();
        }

        return $annotation;
    }

    /**
     * Get annotation matches.
     *
     * @param string $docString The doc string
     *
     * @return array[]|null
     */
    protected function getMatches(string $docString): ?array
    {
        preg_match_all($this->getRegex(), $docString, $matches);

        return $matches;
    }

    /**
     * Set a matched annotation.
     *
     * @param array $matches     The matches
     *                           [
     *                           0 => matches,
     *                           1 => annotation,
     *                           2 => type,
     *                           3 => args,
     *                           4 => var,
     *                           5 => desc
     *                           ]
     * @param int   $index       The index
     * @param array $annotations The annotations list
     *
     * @throws InvalidAnnotationKeyArgument
     * @throws JsonException
     */
    protected function setAnnotation(array $matches, int $index, array &$annotations): void
    {
        $parts = $this->getParts($matches, $index);

        // Get the annotation model from the annotations map
        $annotation = $this->getAnnotationFromMap($parts[Part::TYPE]);

        // Set the annotation's type
        $annotation->setType($parts[Part::TYPE]);

        // If there are properties
        if ($parts[Part::PROPERTIES] !== null && $parts[Part::PROPERTIES]) {
            // Set the annotation's properties to setters if they exist
            $annotation->updateProperties($this->getPropertiesAsArray($parts[Part::PROPERTIES]) ?? []);

            // Having set the properties there's no need to retain this key in
            // the properties
            unset($parts[Part::PROPERTIES]);
        }

        // Set all the matches
        $annotation->setMatches($parts);

        // Set the annotation in the list
        $annotations[] = $annotation;
    }

    /**
     * Get the properties for a matched annotation.
     *
     * @param array $matches The matches
     * @param int   $index   The index
     */
    protected function getParts(array $matches, int $index): array
    {
        $parts = [];

        // Written like this to appease the code coverage gods
        $parts[Part::TYPE]          = $matches[1][$index] ?? null;
        $parts[Part::PROPERTIES]    = $matches[2][$index] ?? null;
        $parts[Part::VARIABLE_TYPE] = $matches[3][$index] ?? null;
        $parts[Part::VARIABLE]      = $matches[4][$index] ?? null;
        $parts[Part::DESCRIPTION]   = $matches[5][$index] ?? null;

        return $this->cleanParts($parts);
    }

    /**
     * Clean the parts.
     *
     * @param array $parts The parts
     */
    protected function cleanParts(array $parts): array
    {
        // If the variable type and description exist but the variable does not
        // then that means the variable regex group captured the
        // first word of the description
        if ($parts[Part::VARIABLE_TYPE] && $parts[Part::DESCRIPTION] && ! $parts[Part::VARIABLE]) {
            // Rectify this by concatenating the type and description
            $parts[Part::DESCRIPTION] = $parts[Part::VARIABLE_TYPE] . $parts[Part::DESCRIPTION];

            // Then unset the type
            unset($parts[Part::VARIABLE_TYPE]);
        }

        // Iterate through the properties
        foreach ($parts as &$property) {
            // Clean each one
            $property = $this->cleanPart($property);
        }

        return $parts;
    }

    /**
     * Clean a part from asterisks and new lines.
     *
     * @param string|null $match The match
     */
    protected function cleanPart(string $match = null): ?string
    {
        if (! $match) {
            return $match;
        }

        return trim(str_replace('*', '', $match));
    }

    /**
     * Determine if a property's value.
     *
     * @param mixed $value The value to check
     */
    protected function determinePropertyValue(mixed $value): mixed
    {
        if (is_array($value)) {
            return $this->determineArrayPropertyValue($value);
        }

        if (! is_string($value)) {
            return $value;
        }

        // Trim the value of spaces
        $value = trim($value);

        // If there was no double colon found there's no need to go further
        if (! str_contains($value, '::')) {
            return $value;
        }

        return $this->determineStaticPropertyValue($value);
    }

    /**
     * Determine an array property's values.
     *
     * @param mixed $value The value to check
     */
    protected function determineArrayPropertyValue(array $value): array
    {
        foreach ($value as &$item) {
            $item = $this->determinePropertyValue($item);
        }

        unset($item);

        return $value;
    }

    /**
     * Determine if a value is a defined static.
     *
     * @param mixed $value The value to check
     */
    protected function determineStaticPropertyValue(mixed $value): mixed
    {
        [$class, $member] = explode('::', $value);
        // Check if the class name is a key defined in the reference classes config
        $class = $this->getClassFromAlias($class);

        // Determine if the value is a constant
        if (defined("$class::$member")) {
            // Set the value as the constant's value
            return constant("$class::$member");
        }

        // Check for static property
        if (property_exists($class, $member)) {
            return $class::$$member;
        }

        // Check for static method
        if (method_exists($class, $member)) {
            return $class::$member();
        }

        return $value;
    }

    /**
     * Get a class alias.
     *
     * @param class-string|string $class The class to check for a reference
     *
     * @return class-string
     */
    protected function getClassFromAlias(string $class): string
    {
        return $this->config['aliases'][$class] ?? $class;
    }
}
