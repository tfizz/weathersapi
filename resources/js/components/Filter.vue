<template>
    <div class="card bg-light  mb-2">
        <div class="card-body">
            <div class="row mb-2">
                <div class="col-md-6">
                    <h5 class="card-title" style="line-height:36px;margin-bottom:0">Filter Weather Data</h5>
                </div>
                <div class="col-md-6 text-right">
                    By
                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                        <label @click="defaultFilter = true" class="btn btn-sm btn-info active">
                            <input type="radio" name="options" id="option1" autocomplete="off" checked> Date
                        </label>
                        <label @click="defaultFilter = false" class="btn btn-sm btn-info">
                            <input type="radio" name="options" id="option2" autocomplete="off"> Coordinates
                        </label>
                    </div>
                </div>
            </div>

            <form @submit.prevent="filterData()">
                <div class="row" v-if="defaultFilter">
                    <div class="col-md-6 form-group">
                        <label for="start">Start date</label>
                        <input class="form-control" required v-model="weather.startDate" type="date" id="start">
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="end">End date</label>
                        <input class="form-control" v-model="weather.endDate" type="date" id="end">
                    </div>
                </div>
                <div class="row" v-if="!defaultFilter">
                    <div class="col-md-6 form-group">
                        <label for="lat">Latitude</label>
                        <input class="form-control" required v-model="weather.location.lat" type="number" id="lat" placeholder="lat">
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="lon">Longitide</label>
                        <input class="form-control" required v-model="weather.location.lon" type="number" id="lon" placeholder="lon">
                    </div>
                </div>

                <button type="submit" v-bind:class="[{ disabled: processing }]" class="btn btn-primary">Submit</button>

                <a href="javascript:;" @click="cancelFilter" v-bind:class="[{ disabled: processing }]" class="btn btn-default">cancel</a>
            </form>
        </div>
    </div>
</template>

<script>
    export default {

        data(){
            return {
                weather: {
                    location: {
                        lat: '',
                        lon: '',
                    },
                    startDate: '',
                    endDate: '',
                },
                defaultFilter: true, // set to true for date
                processing: false
            }
        },

        methods: {
            async filterData(){

                this.processing = true;

                try{
                   let weathers = await fetch(`api/weather?lat=${ this.weather.location.lat}&lon=${ this.weather.location.lon }&start=${ this.weather.startDate }&end=${ this.weather.endDate }`).then(res=>res.json());
                   this.processing = false;
                   if(!weathers.error){
                       this.$emit("updateData",weathers);
                   }
                   else{
                       this.$emit("updateData",[]);
                   }
                }
                catch(err){
                    console.log(err);
                    this.processing = false;
                }
                
            },

            cancelFilter(){
                this.$emit("closeFilter");
                this.weather.location.lon = "";
                this.weather.location.lat = "";
                this.weather.startDate = "";
                this.weather.endDate = "";
            }
        }
    }
</script>