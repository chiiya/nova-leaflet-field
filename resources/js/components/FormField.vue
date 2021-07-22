<template>
  <default-field :field="field" :errors="errors" :full-width-content="true" :show-help-text="showHelpText">
    <template slot="field">
      <div class="map-field z-10 p-0 w-full form-control form-input-bordered overflow-hidden relative">
        <l-map
          ref="map"
          :zoom="zoom"
          :center="center"
          :options="mapOptions"
          @ready="mountSearch"
        >
          <l-tile-layer :url="tileUrl" :subdomains="tileSubdomains"></l-tile-layer>
          <l-marker :lat-lng="center" :draggable="draggable" v-on:update:lat-lng="updateCoordinates"></l-marker>
        </l-map>
      </div>
    </template>
  </default-field>
</template>

<script>
import { FormField, HandlesValidationErrors } from 'laravel-nova';
import { Icon } from 'leaflet';
import { LMap, LTileLayer, LMarker } from 'vue2-leaflet';
import {
  BingProvider,
  HereProvider,
  EsriProvider,
  GoogleProvider,
  LocationIQProvider,
  OpenCageProvider,
  OpenStreetMapProvider,
  SearchControl
} from 'leaflet-geosearch';
import { AddressExtractor } from '../AddressExtractor';

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
      searchOptions: {
        style: 'bar',
        searchLabel: this.field.searchLabel || 'Enter address',
      },
    };
  },

  computed: {
    center() {
      return [this.value.latitude || 0, this.value.longitude || 0];
    },
    tileUrl() {
      return this.field.tileUrl || 'https://{s}.tile.osm.org/{z}/{x}/{y}.png';
    },
    tileSubdomains() {
      return this.field.tileSubdomains || ['a','b','c'];
    },
    draggable() {
      return this.field.draggable || false;
    },
    zoom() {
      return this.field.zoom || 12;
    },
    requestsPopulatingSiblingFields() {
      for (const field of ['address', 'postalCode', 'city', 'country']) {
        if (this.field[field] !== undefined) {
          return true;
        }
      }

      return false;
    },
  },

  created() {
    delete Icon.Default.prototype._getIconUrl;
    Icon.Default.mergeOptions({
      iconRetinaUrl: '/vendor/nova-leaflet-field/marker-icon-2x.png',
      iconUrl: '/vendor/nova-leaflet-field/marker-icon.png',
      shadowUrl: '/vendor/nova-leaflet-field/marker-shadow.png',
    });

    const provider = this.field.searchProvider;
    const key = this.field.searchProviderKey;
    const options = JSON.parse(this.field.searchProviderOptions || '{}');

    if (['google', 'bing', 'here', 'location-iq', 'open-cage'].includes(provider) && !key) {
      console.error('Missing API key for search provider '+provider);

      return;
    }

    switch (this.field.searchProvider) {
      case 'google':
        this.searchOptions.provider = new GoogleProvider(Object.assign({ params: { key }}, options));
        break;
      case 'bing':
        this.searchOptions.provider = new BingProvider({ params: { key }});
        break;
      case 'here':
        this.searchOptions.provider = new HereProvider({ params: { key }});
        break;
      case 'location-iq':
        this.searchOptions.provider = new LocationIQProvider({ params: { key }});
        break;
      case 'open-cage':
        this.searchOptions.provider = new OpenCageProvider({ params: { key }});
        break;
      case 'esri':
        this.searchOptions.provider = new EsriProvider();
        break;
      default:
        const params = key ? { params: { email: key } } : {};
        this.searchOptions.provider = new OpenStreetMapProvider(Object.assign(params, options));
        break;
    }
  },

  methods: {
    /*
     * Set the initial, internal value for the field.
     */
    setInitialValue() {
      this.value = JSON.parse(this.field.value || '{}');
    },

    /**
     * Fill the given FormData object with the field's internal value.
     */
    fill(formData) {
      formData.append(this.field.attribute, JSON.stringify(this.value));
    },

    updateCoordinates(coordinates) {
      this.value.latitude = coordinates.lat;
      this.value.longitude = coordinates.lng;
    },

    mountSearch() {
      const searchControl = new SearchControl(this.searchOptions);
      this.$refs.map.mapObject.addControl(searchControl);
      searchControl.getContainer().onclick = e => { e.stopPropagation(); };
      this.$refs.map.mapObject.on('geosearch/showlocation', (data) => {
        this.value.latitude = data.location.y;
        this.value.longitude = data.location.x;
        this.populateSiblingFields(data.location.raw.address_components);
      })
    },

    populateSiblingFields(location) {
      if (this.field.searchProvider !== 'google' && this.requestsPopulatingSiblingFields) {
        console.error('Populating sibling address fields is only available with the Google provider.');
        return;
      }

      const address = AddressExtractor.format(location);

      if (this.field.address) {
        const format = this.field.populatedAddressFormat || '{street_name} {street_number}';
        const value = format
          .replace('{street_name}', address.street_name)
          .replace('{street_number}', address.street_number);
        this.populateSiblingField(this.field.address, value);
      }
      if (this.field.postalCode) {
        this.populateSiblingField(this.field.postalCode, address.postal_code);
      }
      if (this.field.city) {
        this.populateSiblingField(this.field.city, address.city);
      }
      if (this.field.country) {
        this.populateSiblingField(this.field.country, address.country);
      }
    },

    populateSiblingField(attribute, value) {
      this.$parent.$children.forEach((component) => {
        if(component.field.attribute === attribute){
          component.value = value;
        }
      });
    }
  },
}
</script>
