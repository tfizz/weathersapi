<template>
    <div class="card bg-light  mb-2">
        <div class="card-body">
            <div class="row mb-2">
                <div class="col-md-6">
                    <h5 class="card-title" style="line-height:36px;margin-bottom:0">Delete Weather Data</h5>
                </div>
                <div class="col-md-6 text-right">
                    <button @click="deleteAll" v-bind:class="[{ disabled: processing }]" class="btn btn-sm btn-danger pull-right">Delete All</button>
                </div>
            </div>

            <form @submit.prevent="deleteData()">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Start Date</label>
                        <input class="form-control" required v-model="options.startDate" type="date">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>End Date</label>
                        <input class="form-control" required v-model="options.endDate" type="date">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="">Latitude</label>
                        <input class="form-control" required v-model="options.lat" type="number" id="lat" placeholder="lat">
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="">Longitude</label>
                        <input class="form-control" required v-model="options.lon" type="number" id="lon" placeholder="lon">
                    </div>
                </div>

                <button type="submit" v-bind:class="[{ disabled: processing }]" class="btn btn-primary">Delete</button>

                <a href="javascript:;" @click="cancelDelete" v-bind:class="[{ disabled: processing }]" class="btn btn-default">cancel</a>
            </form>
        </div>
    </div>
</template>

<script>
    export default {

        data(){
            return {
                options: {
                    startDate: '',
                    endDate: '',
                    lat: '',
                    lon: ''
                },
                processing: false
            }
        },

        methods: {
            async deleteData(){

                if(confirm("Are you sure you want to delete records?")){
                    this.processing = true;

                    try{
                        let res = await fetch(`api/erase`,{
                            method: "delete",
                            body: JSON.stringify({ start: this.options.startDate, end: this.options.endDate, lat: this.options.lat, lon: this.options.lon}),
                            headers: {
                                "content-type": "application/json"
                            }
                        }).then(res=>res.json());

                        if(res.message){
                            alert(res.message);
                            this.options.lon = "";
                            this.options.lat = "";
                            this.options.startDate = "";
                            this.options.endDate = "";

                            this.$emit("refreshData");
                        }
                        else{
                            alert(res.error);
                        }
                        
                        this.processing = false;
                    }
                    catch(err){
                        console.log(err);
                        this.processing = false;
                    }
                }
                
            },

            async deleteAll(){

                if(confirm("Are you sure you want to delete all records?")){
                    this.processing = true;

                    try{
                    let res = await fetch(`api/erase`,{
                        method: "delete",
                        headers: {
                            "content-type": "application/json"
                        }
                    }).then(res=>res.json());


                    if(res.message){
                        alert(res.message);
                        this.options.lon = "";
                        this.options.lat = "";
                        this.options.startDate = "";
                        this.options.endDate = "";

                        this.$emit("refreshData");
                    }
                    else{
                        alert(res.error);
                    }

                    this.processing = false;
                    }
                    catch(err){
                        console.log(err);
                        this.processing = false;
                    }
                }
            },

            cancelDelete(){
                this.$emit("closeDelete");
                this.options.lon = "";
                this.options.lat = "";
                this.options.startDate = "";
                this.options.endDate = "";
            }
        }
    }
</script>