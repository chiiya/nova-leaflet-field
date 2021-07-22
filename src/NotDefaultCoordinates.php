<?php

namespace Dermingo\MapField;

use Illuminate\Contracts\Validation\Rule;

class NotDefaultCoordinates implements Rule
{
    protected array $defaults;
    protected string $errorMessage;

    /**
     * NotDefaultCoordinates constructor.
     */
    public function __construct(array $defaults, string $errorMessage)
    {
        $this->defaults = $defaults;
        $this->errorMessage = $errorMessage;
    }

    /**
     * @inheritDoc
     */
    public function passes($attribute, $value): bool
    {
        $coordinates = json_decode($value, false);
        $latitude = (float) $coordinates->latitude;
        $longitude = (float) $coordinates->longitude;

        return
            abs($this->defaults[0] - $latitude) < PHP_FLOAT_EPSILON
            && abs($this->defaults[1] - $longitude) < PHP_FLOAT_EPSILON;
    }

    /**
     * @inheritDoc
     */
    public function message(): string
    {
        return $this->errorMessage;
    }
}
