<template>
    <div class="card bg-light  mb-2">
        <div class="card-body">
            <h5 class="card-title">Add Weather Data</h5>

            <form @submit.prevent="addData()">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <!-- <label for="id">Id</label> -->
                        <input class="form-control" required v-model="weather.id" type="number" id="id" placeholder="id">
                    </div>
                    <div class="col-md-6 form-group">
                        <!-- <label for="date">Date</label> -->
                        <input class="form-control" required v-model="weather.date" type="date" id="date" placeholder="date">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 form-group">
                        <!-- <label for="state">State</label> -->
                        <input class="form-control" required type="text" v-model="weather.location.state" id="state" placeholder="state">
                    </div>
                    <div class="col-md-3 form-group">
                        <!-- <label for="city">City</label> -->
                        <input class="form-control" required type="text" v-model="weather.location.city" id="city" placeholder="city">
                    </div>
                    <div class="col-md-3 form-group">
                        <!-- <label for="lat">Lat</label> -->
                        <input class="form-control" required type="text" v-model="weather.location.lat" id="lat" placeholder="lat">
                    </div>
                    <div class="col-md-3 form-group">
                        <!-- <label for="lon">Lon</label> -->
                        <input class="form-control" required type="text" v-model="weather.location.lon" id="lon" placeholder="lon">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 form-group">
                        <!-- <label for="temp">Temperatures</label> -->
                        <input type="text" class="form-control" required v-model="weather.temperature" id="temp" placeholder="23.2,23.5,27.3">
                    </div>
                </div>

                <button type="submit" v-bind:class="[{ disabled: processing }]" class="btn btn-primary">Submit</button>

                <a href="javascript:;"  @click="cancelAdd" v-bind:class="[{ disabled: processing }]" class="btn btn-light">cancel</a>
            </form>
        </div>
    </div>
</template>

<script>
    export default {

        data(){
            return {
                weathers: [],
                weather_id: '',
                weather: {
                    id: '',
                    date: '',
                    location: {
                        lat: '',
                        lon: '',
                        city: '',
                        state: ''
                    },
                    temperature: []
                },
                edit: false,
                processing: false
            }
        },

        methods: {
            async addData(){

                if(isNaN(parseFloat(this.weather.location.lat)) || isNaN(parseFloat(this.weather.location.lon))){
                    alert("Invalid coordinates");
                    return;
                }

                // split temperature into array of floating point numbers
                var temp_string = this.weather.temperature;
                var temps = this.weather.temperature.split(",").map(function(item){
                    return parseFloat(item,10);
                });
                this.weather.temperature = temps;

                if(this.edit === false){
                    // add data
                    var vm = this;
                    try{
                        vm.processing = true;

                        let result = await fetch("api/weather",{
                            method: "post",
                            body: JSON.stringify(this.weather),
                            headers: {
                                'content-type': 'application/json'
                            }
                        }).then(res => res.json());

                        if(result.message){
                            this.weather.id = '';
                            this.weather.date = '';
                            this.weather.location.state = '';
                            this.weather.location.city = '';
                            this.weather.location.lat = '';
                            this.weather.location.lon = '';
                            this.weather.temperature = '';

                            vm.$emit("refreshWeathers");

                            alert(result.message);
                        }
                        else{
                            this.weather.temperature = temp_string;
                            alert(result.error.temperature ? result.error.temperature[0] : result.error);
                        }

                        vm.processing = false;
                    }
                    catch(err){
                        vm.processing = false;
                        console.log(err);
                    }         
                }
                else{
                    // update data
                    var vm = this;
                    try{
                        vm.processing = true;

                        let result = await fetch("api/weather",{
                            method: "put",
                            body: JSON.stringify(this.weather),
                            headers: {
                                'content-type': 'application/json'
                            }
                        }).then(res => res.json());

                        if(result.message){
                            this.weather.id = '';
                            this.weather.date = '';
                            this.weather.location.state = '';
                            this.weather.location.city = '';
                            this.weather.location.lat = '';
                            this.weather.location.lon = '';
                            this.weather.temperature = '';

                            vm.$emit("refreshWeathers");

                            alert(result.message);
                        }
                        else{
                            this.weather.temperature = temp_string;
                            alert(result.error.temperature ? result.error.temperature[0] : result.error);
                        }

                        vm.processing = false;
                    }
                    catch(err){
                        vm.processing = false;
                        console.log(err);
                    }
                }
            },

            cancelAdd(){
                this.$emit("closeAdd");
            }
        }
    }
</script>
