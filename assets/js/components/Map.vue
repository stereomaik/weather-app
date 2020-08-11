<template>
  <div>
    <GmapMap @click="checkWeather"
        :center="startingPosition"
        :zoom="10"
        map-type-id="terrain"
        style="width: 100%; height: 500px"
    >
      <GmapMarker
          :position="marker.position"
          :clickable="true"
          @click="checkWeather"
      />
    </GmapMap>
    <div class="overlay flex-centered" v-show="isModalVisible">
      <div class="card" style="width: 40%; min-height: 50%">
        <div v-if="isLoading" style="height:100%">
          <div class="card-header text-center">
            Checking weather for: ({{ marker.position.lat.toFixed(2) }}, {{ marker.position.lng.toFixed(2) }})
          </div>
          <div class="flex-centered">
            <div class="spinner-border text-success" role="status">
              <span class="sr-only">Loading...</span>
            </div>
          </div>
        </div>
        <div v-else>
          <div class="card-header text-center">
            Weather data
          </div>
          <ul class="list-group list-group-flush" v-if="isSuccess">
            <li class="list-group-item">
              <strong>City: </strong> {{ weather.city }}
            </li>
            <li class="list-group-item">
              <strong>Latitude: </strong> {{ marker.position.lat.toFixed(2) }}
            </li>
            <li class="list-group-item">
              <strong>Longitude: </strong> {{ marker.position.lng.toFixed(2) }}
            </li>
            <li class="list-group-item">
              <strong>Temperature: </strong> {{ weather.temperature }}&deg;C
            </li>
            <li class="list-group-item">
              <strong>Wind speed: </strong> {{ weather.wind }}m/s
            </li>
            <li class="list-group-item">
              <strong>Clouds: </strong> {{ weather.clouds }}%
            </li>
            <li class="list-group-item">
              <strong>Description: </strong> {{ weather.description }}
            </li>
          </ul>
          <div class="alert alert-danger" v-else>
            We were unable to check the weather. Please try with different coordinates.<br/>
            Should the problem persist, please contact website's admins.
          </div>
          <div class="flex-centered mt-4 mb-4">
            <button class="btn btn-success" @click="closeModal">Zamknij</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
  const axios = require('axios');

  export default {
    data() {
      return {
        isModalVisible: false,
        isLoading: true,
        isSuccess: false,
        startingPosition: {
          lat: 50.185168,
          lng: 18.9142343
        },
        marker: {
          position: {
            lat: 0,
            lng: 0
          }
        },
        weather: {
          city: '',
          temperature: 0,
          clouds: 0,
          wind: 0,
          description: ''
        }
      }
    },
    methods: {
      checkWeather(e) {
        this.marker.position.lat = e.latLng.lat();
        this.marker.position.lng = e.latLng.lng();
        this.isModalVisible = true;

        let pointOfReference = this;
        axios.post('/get-weather', {
            latitude: this.marker.position.lat,
            longitude: this.marker.position.lng
          })
          .then(function (response) {
            pointOfReference.setWeather(response.data);
            pointOfReference.isSuccess = true;
            pointOfReference.isLoading = false;
          })
          .catch(function (error) {
            pointOfReference.isSuccess = false;
            pointOfReference.isLoading = false;
          });
      },
      setWeather(data) {
        this.marker.position.lat = data.latitude;
        this.marker.position.lng = data.longitude;
        this.weather.city = data.city;
        this.weather.temperature = data.temperature;
        this.weather.clouds = data.clouds;
        this.weather.wind = data.windSpeed;
        this.weather.description = data.description;
      },
      closeModal() {
        this.isModalVisible = false;
        this.isLoading = true;
        this.isSuccess = false;
      }
    }
  }
</script>