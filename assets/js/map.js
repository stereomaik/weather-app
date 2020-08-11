import Vue from 'vue';
import * as VueGoogleMaps from 'vue2-google-maps'
import Map from './components/Map';
import '../css/map.css';

Vue.use(VueGoogleMaps, {
    load: {
        key: 'AIzaSyB_vafIN5HGpdJXrpAD0IdqRL0bUn_2H5Y',
    },
})

new Vue({
    el: '#map',
    render: h => h(Map)
});