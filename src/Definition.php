<?php

declare(strict_types=1);

namespace Brick\DI;

/**
 * Base class for definitions.
 */
abstract class Definition
{
    private Scope|null $scope = null;

    /**
     * Changes the scope of this definition.
     */
    public function in(Scope $scope) : static
    {
        $this->scope = $scope;

        return $this;
    }

    /**
     * Resolves the value of this definition, according to the current scope.
     *
     * This method is for internal use by the Container.
     *
     * @internal
     */
    public function get(Container $container) : mixed
    {
        if ($this->scope === null) {
            $this->scope = $this->getDefaultScope();
        }

        return $this->scope->get($this, $container);
    }

    /**
     * Resolves the value of this definition, regardless of the Scope.
     *
     * This method is for internal use by the Scopes.
     *
     * @internal
     */
    abstract public function resolve(Container $container) : mixed;

    /**
     * Returns the default Scope for this definition when not set explicitly.
     *
     * @return Scope
     */
    abstract protected function getDefaultScope() : Scope;
}
