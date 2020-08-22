<?php

declare(strict_types = 1);

namespace Constup\PharProcessor\Service;

use Constup\PharProcessor\Service\SubObject\FqnClassNameAssociationImmutableObject;
use Constup\PharProcessor\Service\SubObject\FqnClassNameAssociationImmutableObjectInterface;
use Phar;
use PharFileInfo;
use RecursiveIteratorIterator;

/**
 * Class PharCrawlerService
 *
 * @package Constup\PharProcessor\Service
 */
class PharCrawlerService
{
    /**
     * @param string $pharArchiveLocation
     * @param string[] $includedNamespaces
     * @param int[] $phpFileTypes
     * @return FqnClassNameAssociationImmutableObjectInterface[]
     */
    public function findAllFqns(
        string $pharArchiveLocation,
        array $includedNamespaces,
        array $phpFileTypes = [T_CLASS, T_INTERFACE, T_TRAIT]
    ): array
    {
        $fqns = [];

        $phar = new Phar($pharArchiveLocation);
        /** @var PharFileInfo $file */
        foreach (new RecursiveIteratorIterator($phar) as $file) {
            if ($file->getExtension() == 'php' && $file->isReadable() && $file->isFile()) {
                $content = $file->getContent();
                $tokens = token_get_all($content);
                $namespace = '';
                for ($index = 0; isset($tokens[$index]); ++$index) {
                    if (!isset($tokens[$index][0])) {
                        continue;
                    }
                    if (T_NAMESPACE === $tokens[$index][0]) {
                        $index += 2; // Skip namespace keyword and whitespace
                        while (isset($tokens[$index]) && is_array($tokens[$index])) {
                            $namespace .= $tokens[$index++][1];
                        }
                    }
                    if (in_array($tokens[$index][0], $phpFileTypes)
                        && T_WHITESPACE === $tokens[$index + 1][0] && T_STRING === $tokens[$index + 2][0]) {
                        $index += 2; // Skip class keyword and whitespace
                        $fqn = $namespace . '\\' . $tokens[$index][1];
                        if ($this->shouldIncludeFqn($fqn, $includedNamespaces)) {
                            $fqns[] = new FqnClassNameAssociationImmutableObject($fqn, $tokens[$index][1]);
                        }

                        break;
                    }
                }
            }
        }

        return $fqns;
    }

    /**
     * @param string $fqn
     * @param string[] $includedNamespaces
     *
     * @return bool
     */
    private function shouldIncludeFqn(string $fqn, array $includedNamespaces): bool
    {
        if (!empty($includedNamespaces)) {
            foreach ($includedNamespaces as $includedNamespace) {
                if (strpos($fqn, $includedNamespace) === 1) {
                    return true;
                }
            }
        }

        return false;
    }

}
