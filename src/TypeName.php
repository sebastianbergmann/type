<?php declare(strict_types=1);
/*
 * This file is part of sebastian/type.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\Type;

use function assert;
use function strrpos;
use function substr;
use ReflectionClass;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for this library
 */
final readonly class TypeName
{
    private ?string $namespaceName;

    /**
     * @var non-empty-string
     */
    private string $simpleName;

    /**
     * @var non-empty-string
     */
    private string $qualifiedName;

    /**
     * @param class-string $fullClassName
     */
    public static function fromQualifiedName(string $fullClassName): self
    {
        if ($fullClassName[0] === '\\') {
            $fullClassName = substr($fullClassName, 1);
        }

        $position = strrpos($fullClassName, '\\');

        if ($position === false) {
            assert($fullClassName !== '');

            return new self(null, $fullClassName);
        }

        $simpleName = substr($fullClassName, $position + 1);

        assert($simpleName !== '');

        return new self(substr($fullClassName, 0, $position), $simpleName);
    }

    /**
     * @param ReflectionClass<*> $type
     */
    public static function fromReflection(ReflectionClass $type): self
    {
        $simpleName = $type->getShortName();

        assert($simpleName !== '');

        return new self(
            $type->getNamespaceName(),
            $simpleName,
        );
    }

    /**
     * @param non-empty-string $simpleName
     */
    public function __construct(?string $namespaceName, string $simpleName)
    {
        if ($namespaceName === '') {
            $namespaceName = null;
        }

        $this->namespaceName = $namespaceName;
        $this->simpleName    = $simpleName;
        $this->qualifiedName = $namespaceName === null ? $simpleName : $namespaceName . '\\' . $simpleName;
    }

    public function namespaceName(): ?string
    {
        return $this->namespaceName;
    }

    /**
     * @return non-empty-string
     */
    public function simpleName(): string
    {
        return $this->simpleName;
    }

    /**
     * @return non-empty-string
     */
    public function qualifiedName(): string
    {
        return $this->qualifiedName;
    }

    public function isNamespaced(): bool
    {
        return $this->namespaceName !== null;
    }
}
