<?php

declare(strict_types = 1);
/**
 * This file has been automatically generated by Constup Automatrix.
 * https://constup.com/automatrix
 */

namespace Constup\PharProcessor\Service\SubObject;

use Constup\Automatrix\LanguagePhp\Version0702\DesignPatternInterfaces\JsonMapperInterface;
use Constup\Automatrix\LanguagePhp\Version0702\JsonMapper\AbstractJsonMapper;
use stdClass;

class FqnClassNameAssociationJsonMapper extends AbstractJsonMapper implements JsonMapperInterface
{
    /**
     * @param object $json
     *
     * @return FqnClassNameAssociationImmutableObjectInterface|null
     */
    public function fromJson(object $json): ?FqnClassNameAssociationImmutableObjectInterface
    {
        $fqn = $json->fqn;
        $className = $json->className;

        return new FqnClassNameAssociationImmutableObject($fqn, $className);
    }

    /**
     * @param FqnClassNameAssociationImmutableObjectInterface $object
     *
     * @return object|null
     */
    public function toJson(FqnClassNameAssociationImmutableObjectInterface $object): ?object
    {
        $fqn = $object->getFqn();
        $className = $object->getClassName();

        $result = new stdClass();
        $result->automatrixDeserializer = get_class($object);
        $result->fqn = $fqn;
        $result->className = $className;

        return $result;
    }
}
