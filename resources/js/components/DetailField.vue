<template>
  <panel-item :field="field">
    <div slot="value">
      <div class="map-field z-10 p-0 w-full form-control form-input-bordered overflow-hidden relative">
        <l-map
          :zoom="zoom"
          :center="center"
          :options="mapOptions"
        >
          <l-tile-layer :url="tileUrl" :subdomains="tileSubdomains"></l-tile-layer>
          <l-marker :lat-lng="center"></l-marker>
        </l-map>
      </div>
    </div>
  </panel-item>
</template>

<script>
import { FormField, HandlesValidationErrors } from 'laravel-nova';
import { Icon } from 'leaflet';
import { LMap, LTileLayer, LMarker } from 'vue2-leaflet';

export default {
  components: {
    LMap,
    LTileLayer,
    LMarker,
  },
  mixins: [FormField, HandlesValidationErrors],

  props: ['resourceName', 'resourceId', 'field'],

  data() {
    return {
      mapOptions: {
        doubleClickZoom: 'center',
        scrollWheelZoom: 'center',
        touchZoom: 'center',
      },
    };
  },

  computed: {
    center() {
      return [this.value.latitude || 0, this.value.longitude || 0];
    },
    zoom() {
      return this.field.zoom || 12;
    },
    tileUrl() {
      return this.field.tileUrl || 'https://{s}.tile.osm.org/{z}/{x}/{y}.png';
    },
    tileSubdomains() {
      return this.field.tileSubdomains || ['a','b','c'];
    },
  },

  created() {
    delete Icon.Default.prototype._getIconUrl;
    Icon.Default.mergeOptions({
      iconRetinaUrl: '/vendor/nova-leaflet-field/marker-icon-2x.png',
      iconUrl: '/vendor/nova-leaflet-field/marker-icon.png',
      shadowUrl: '/vendor/nova-leaflet-field/marker-shadow.png',
    });
  },

  methods: {
    /*
     * Set the initial, internal value for the field.
     */
    setInitialValue() {
      this.value = JSON.parse(this.field.value || '{}');
    },
  },
}
</script>
