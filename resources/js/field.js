Nova.booting((Vue, router, store) => {
  Vue.component('index-leaflet-field', require('./components/IndexField'))
  Vue.component('detail-leaflet-field', require('./components/DetailField'))
  Vue.component('form-leaflet-field', require('./components/FormField'))
})
