(window.webpackJsonp=window.webpackJsonp||[]).push([["map"],{"6LoH":function(t,e,i){"use strict";i.r(e);var s=i("oCYn"),a=i("dV7z"),r=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("div",[i("GmapMap",{staticStyle:{width:"100%",height:"500px"},attrs:{center:t.startingPosition,zoom:10,"map-type-id":"terrain"},on:{click:t.checkWeather}},[i("GmapMarker",{attrs:{position:t.marker.position,clickable:!0},on:{click:t.checkWeather}})],1),t._v(" "),i("div",{directives:[{name:"show",rawName:"v-show",value:t.isModalVisible,expression:"isModalVisible"}],staticClass:"overlay flex-centered"},[i("div",{staticClass:"card",staticStyle:{width:"40%","min-height":"50%"}},[t.isLoading?i("div",{staticStyle:{height:"100%"}},[i("div",{staticClass:"card-header text-center"},[t._v("\n          Checking weather for: ("+t._s(t.marker.position.lat.toFixed(2))+", "+t._s(t.marker.position.lng.toFixed(2))+")\n        ")]),t._v(" "),t._m(0)]):i("div",[i("div",{staticClass:"card-header text-center"},[t._v("\n          Weather data\n        ")]),t._v(" "),t.isSuccess?i("ul",{staticClass:"list-group list-group-flush"},[i("li",{staticClass:"list-group-item"},[i("strong",[t._v("City: ")]),t._v(" "+t._s(t.weather.city)+"\n          ")]),t._v(" "),i("li",{staticClass:"list-group-item"},[i("strong",[t._v("Latitude: ")]),t._v(" "+t._s(t.marker.position.lat.toFixed(2))+"\n          ")]),t._v(" "),i("li",{staticClass:"list-group-item"},[i("strong",[t._v("Longitude: ")]),t._v(" "+t._s(t.marker.position.lng.toFixed(2))+"\n          ")]),t._v(" "),i("li",{staticClass:"list-group-item"},[i("strong",[t._v("Temperature: ")]),t._v(" "+t._s(t.weather.temperature)+"°C\n          ")]),t._v(" "),i("li",{staticClass:"list-group-item"},[i("strong",[t._v("Wind speed: ")]),t._v(" "+t._s(t.weather.wind)+"m/s\n          ")]),t._v(" "),i("li",{staticClass:"list-group-item"},[i("strong",[t._v("Clouds: ")]),t._v(" "+t._s(t.weather.clouds)+"%\n          ")]),t._v(" "),i("li",{staticClass:"list-group-item"},[i("strong",[t._v("Description: ")]),t._v(" "+t._s(t.weather.description)+"\n          ")])]):i("div",{staticClass:"alert alert-danger"},[t._v("\n          We were unable to check the weather. Please try with different coordinates."),i("br"),t._v("\n          Should the problem persist, please contact website's admins.\n        ")]),t._v(" "),i("div",{staticClass:"flex-centered mt-4 mb-4"},[i("button",{staticClass:"btn btn-success",on:{click:t.closeModal}},[t._v("Zamknij")])])])])])],1)};r._withStripped=!0;i("pNMO"),i("4Brf");var n=i("vDqi"),o={data:function(){return{isModalVisible:!1,isLoading:!0,isSuccess:!1,startingPosition:{lat:50.185168,lng:18.9142343},marker:{position:{lat:0,lng:0}},weather:{city:"",temperature:0,clouds:0,wind:0,description:""}}},methods:{checkWeather:function(t){this.marker.position.lat=t.latLng.lat(),this.marker.position.lng=t.latLng.lng(),this.isModalVisible=!0;var e=this;n.post("/get-weather",{latitude:this.marker.position.lat,longitude:this.marker.position.lng}).then((function(t){e.setWeather(t.data),e.isSuccess=!0,e.isLoading=!1})).catch((function(t){e.isSuccess=!1,e.isLoading=!1}))},setWeather:function(t){this.marker.position.lat=t.latitude,this.marker.position.lng=t.longitude,this.weather.city=t.city,this.weather.temperature=t.temperature,this.weather.clouds=t.clouds,this.weather.wind=t.windSpeed,this.weather.description=t.description},closeModal:function(){this.isModalVisible=!1,this.isLoading=!0,this.isSuccess=!1}}},l=i("KHd+"),c=Object(l.a)(o,r,[function(){var t=this.$createElement,e=this._self._c||t;return e("div",{staticClass:"flex-centered"},[e("div",{staticClass:"spinner-border text-success",attrs:{role:"status"}},[e("span",{staticClass:"sr-only"},[this._v("Loading...")])])])}],!1,null,null,null);c.options.__file="assets/js/components/Map.vue";var d=c.exports;i("Sr92");s.a.use(a,{load:{key:"AIzaSyB_vafIN5HGpdJXrpAD0IdqRL0bUn_2H5Y"}}),new s.a({el:"#map",render:function(t){return t(d)}})},Sr92:function(t,e,i){}},[["6LoH","runtime",0]]]);