<template>
    <div>
        <div v-show="showOptions" class="panel panel-default mb-2">
            <div class="panel-body">
                <h6 style="display:inline-block;line-height:38px;margin-bottom:0" class="panel-title">Records</h6>
                
                <div class="float-right">
                    <button @click="toggleAdd" class="btn btn-info">Add</button>
                    <button @click="toggleDelete" class="btn btn-warning">Erase</button>
                    <button @click="toggleFilter" class="btn btn-light">Filter</button>
                    <!-- <button @click="toggleTemp" class="btn btn-light">Temperature</button> -->
                </div>

                <div style="clear:both;"></div>
            </div>
        </div>

        <appform :record="weather" v-show="showAdd" v-on:closeAdd="toggleAdd" v-on:refreshWeathers="fetchRecords"></appform>

        <appfilter v-show="showFilter" v-on:closeFilter="toggleFilter" v-on:updateData="updateData"></appfilter>

        <appDelete v-show="showDelete" v-on:closeDelete="toggleDelete" v-on:refreshData="fetchRecords"></appDelete>


        <div class="row">
            <div class="col-md-4" v-for="weather in weathers" v-bind:key="weather.id">
                <div class="card mb-2">
                    <img class="card-img-top" src="https://media.istockphoto.com/vectors/green-city-vector-id534213886?k=6&m=534213886&s=612x612&w=0&h=5aRmqFkNOLPGI8uSfgsXg2a4R-mDW4r-nC41YjjmQ8Q=">
                    <div class="card-body">
                        <h5 class="card-title">{{ weather.location.state }}</h5>
                        <h6 class="card-subtitle mb-2 text-muted">{{ weather.location.city }} ({{ weather.location.lat }},{{ weather.location.lon }})</h6>
                        <p class="card-text">
                            <span style="margin-right:2px" class="badge badge-pill badge-light" v-for="(temp, index) in weather.temperature" v-bind:key="index">
                                {{ temp }}F
                            </span>
                        </p>
                        <a class="card-link text-muted">{{ weather.date }}</a>
                        <a href="javascript:;" @click="edit(weather)" class="card-link">edit</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {

        data(){
            return {
                weathers: [],
                cachedWeathers: [],
                weather_id: '',
                weather: {
                    id: '',
                    date: '',
                    location: {},
                    temperature: []
                },
                showAdd: false,
                showFilter: false,
                showDelete: false,
                showTemp: false,
                showOptions: true
            }
        },

        created(){
            this.fetchRecords();
        },

        methods: {
            async fetchRecords(){
               try{
                   let weathers = await fetch(`api/weather`).then(res=>res.json());
                   this.weathers = weathers;
                   this.weathers.sort(function(a,b){
                       return new Date(b.date) - new Date(a.date);
                   });
                   this.cachedWeathers = this.weathers;
               }
               catch(err){
                   console.log(err);
               }
            },

            toggleAdd(){
                this.showAdd = !this.showAdd;
                this.showOptions = !this.showOptions;
            },

            toggleFilter(){
                this.showFilter = !this.showFilter;
                this.showOptions = !this.showOptions;
                this.resetData();
            },

            toggleTemp(){
                this.showTemp = !this.showTemp;
                this.showOptions = !this.showOptions;
                this.resetData();
            },

            toggleDelete(){
                this.showDelete = !this.showDelete;
                this.showOptions = !this.showOptions;
            },

            updateData(data){
                this.weathers = data.sort(function(a,b){
                    return new Date(b.date) - new Date(a.date);
                });
            },

            resetData(){
                this.weathers = this.cachedWeathers;
            },

            edit(record){
                this.weather = record;
                this.toggleAdd();
            }

        }
    }
</script>


