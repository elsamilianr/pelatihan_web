<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Field;

/**
 * Peta interaktif dengan pin yang bisa digeser.
 * Otomatis mengisi field `latitude` dan `longitude` pada form yang sama.
 */
class MapPicker extends Field
{
    protected string $view = 'forms.components.map-picker';

    protected string $latField = 'latitude';
    protected string $lngField = 'longitude';
    protected int|string $mapHeight = 380;
    protected float $defaultLat = -6.2;
    protected float $defaultLng = 106.82;
    protected int $defaultZoom = 13;
    protected string $pinColor = '#e11d48';

    protected function setUp(): void
    {
        parent::setUp();

        // Field ini hanya UI, tidak disimpan ke database.
        $this->dehydrated(false);
        $this->columnSpanFull();
    }

    public function latitudeField(string $name): static
    {
        $this->latField = $name;

        return $this;
    }

    public function longitudeField(string $name): static
    {
        $this->lngField = $name;

        return $this;
    }

    public function height(int|string $height): static
    {
        $this->mapHeight = $height;

        return $this;
    }

    public function defaultLocation(float $lat, float $lng, int $zoom = 13): static
    {
        $this->defaultLat = $lat;
        $this->defaultLng = $lng;
        $this->defaultZoom = $zoom;

        return $this;
    }

    public function pinColor(string $color): static
    {
        $this->pinColor = $color;

        return $this;
    }

    public function getConfig(): array
    {
        $container = $this->getContainer()->getStatePath();
        $prefix = $container ? $container . '.' : '';

        return [
            'latPath' => $prefix . $this->latField,
            'lngPath' => $prefix . $this->lngField,
            'height' => is_numeric($this->mapHeight) ? $this->mapHeight . 'px' : $this->mapHeight,
            'defaultLat' => $this->defaultLat,
            'defaultLng' => $this->defaultLng,
            'defaultZoom' => $this->defaultZoom,
            'pinColor' => $this->pinColor,
        ];
    }
}
