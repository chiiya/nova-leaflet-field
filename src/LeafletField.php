<?php

namespace Dermingo\MapField;

use Illuminate\Support\Facades\Log;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;

class LeafletField extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'leaflet-field';

    /**
     * Indicates if the element should be shown on the index view.
     */
    public $showOnIndex = false;

    protected bool $allowDefaultCoordinates = false;
    protected array $defaultCoordinates = [0.0, 0.0];
    protected string $errorMessage = 'Please select a valid location';

    /**
     * Should the map marker be draggable?
     * Default: false.
     */
    public function draggable(bool $draggable = true): self
    {
        return $this->withMeta(['draggable' => $draggable]);
    }

    /**
     * Set the search provider
     * @see SearchProvider
     */
    public function searchProvider(string $provider): self
    {
        return $this->withMeta(['searchProvider' => $provider]);
    }

    /**
     * Set the search provider API key.
     */
    public function searchProviderKey(string $key): self
    {
        return $this->withMeta(['searchProviderKey' => $key]);
    }

    /**
     * Provide additional parameters to the leaflet geo-search provider.
     */
    public function searchProviderOptions(array $options): self
    {
        return $this->withMeta(['searchProviderOptions' => json_encode($options)]);
    }

    /**
     * Customize the search label
     */
    public function searchLabel(string $label): self
    {
        return $this->withMeta(['searchLabel' => $label]);
    }

    /**
     * Set the latitude field name on the model.
     */
    public function latitudeField(string $field): self
    {
        return $this->withMeta(['latitudeField' => $field]);
    }

    /**
     * Set the longitude field name on the model.
     */
    public function longitudeField(string $field): self
    {
        return $this->withMeta(['longitudeField' => $field]);
    }

    /**
     * Set the zoom value (defaults to 12).
     */
    public function zoom(string $zoom): self
    {
        return $this->withMeta(['zoom' => $zoom]);
    }

    /**
     * Provide a custom leaflet tile URL.
     */
    public function tileUrl(string $url): self
    {
        return $this->withMeta(['tileUrl' => $url]);
    }

    /**
     * Only for Google provider!
     * Populate the value of another field (address) with the geo-coding result.
     */
    public function populateAddress(string $field = 'address'): self
    {
        return $this->withMeta(['address' => $field]);
    }

    /**
     * Only for Google provider!
     * Populate the value of another field (postal code) with the geo-coding result.
     */
    public function populatePostalCode(string $field = 'postal_code'): self
    {
        return $this->withMeta(['postalCode' => $field]);
    }

    /**
     * Only for Google provider!
     * Populate the value of another field (city) with the geo-coding result.
     */
    public function populateCity(string $field = 'city'): self
    {
        return $this->withMeta(['city' => $field]);
    }

    /**
     * Only for Google provider!
     * Populate the value of another field (country) with the geo-coding result.
     */
    public function populateCountry(string $field = 'country'): self
    {
        return $this->withMeta(['country' => $field]);
    }

    /**
     * What format should the populated address have?
     * Defaults to '{street_name} {street_number}'
     * Other examples:
     *  - '{street_number}, {street_name}'
     */
    public function populatedAddressFormat(string $format): self
    {
        return $this->withMeta(['populatedAddressFormat' => $format]);
    }

    /**
     * Should the default coordinates be allowed when the field is required?
     */
    public function allowDefaultCoordinates(): self
    {
        $this->allowDefaultCoordinates = true;

        return $this;
    }

    /**
     * Override default coordinates.
     */
    public function defaultCoordinates(float $latitude, float $longitude): self
    {
        $this->defaultCoordinates = [$latitude, $longitude];

        return $this;
    }

    /**
     * Override the error message when no location is selected and the field is required.
     */
    public function errorMessage(string $message): self
    {
        $this->errorMessage = $message;

        return $this;
    }

    /**
     * Resolve VueJS value prop.
     */
    public function resolve($resource, $attribute = null)
    {
        $latitudeField = $this->meta['latitudeField'] ?? 'latitude';
        $longitudeField = $this->meta['longitudeField'] ?? 'longitude';

        $this->value = json_encode([
            'latitude' => (float) $resource->{$latitudeField},
            'longitude' => (float) $resource->{$longitudeField},
            'latitude_field' => $latitudeField,
            'longitude_field' => $longitudeField,
        ]);
    }

    /**
     * Get the validation rules for this field.
     */
    public function getRules(NovaRequest $request): array
    {
        $rules = is_callable($this->rules)
            ? call_user_func($this->rules, $request)
            : $this->rules;
        if (in_array('required', $rules) && !$this->allowDefaultCoordinates) {
            $rules[] = new NotDefaultCoordinates($this->defaultCoordinates, $this->errorMessage);
        }

        return [$this->attribute => $rules];
    }

    /**
     * Fill latitude and longitude on the model from request data.
     */
    protected function fillAttributeFromRequest(NovaRequest $request, $requestAttribute, $model, $attribute)
    {
        if ($request->exists($requestAttribute)) {
            $result = json_decode($request->{$requestAttribute}, false);

            $model->{$result->latitude_field} = $this->isNullValue($result->latitude)
                ? null
                : $result->latitude;
            $model->{$result->longitude_field} = $this->isNullValue($result->longitude)
                ? null
                : $result->longitude;
        }
    }
}
