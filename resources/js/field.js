Nova.booting((Vue, router, store) => {
  Vue.component('index-leaflet-field', require('./components/IndexField').default)
  Vue.component('detail-leaflet-field', require('./components/DetailField').default)
  Vue.component('form-leaflet-field', require('./components/FormField').default)
})
