<template>
    <div class="card bg-light  mb-2">
        <div class="card-body">
            <h5 class="card-title">Temperature</h5>

            <form @submit.prevent="tempData()">
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
                <!-- <div class="row">
                    <div class="col-md-6 form-group">
                        <input class="form-control" required v-model="options.state" type="number" id="state" placeholder="state">
                    </div>
                    <div class="col-md-6 form-group">
                        <input class="form-control" required v-model="options.city" type="number" id="city" placeholder="city">
                    </div>
                </div> -->

                <button type="submit" v-bind:class="[{ disabled: processing }]" class="btn btn-primary">Submit</button>

                <a href="javascript:;" @click="cancelTemp" v-bind:class="[{ disabled: processing }]" class="btn btn-default">cancel</a>
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
                    state: '',
                    city: ''
                },
                processing: false
            }
        },

        methods: {
            async tempData(){

               this.processing = true;

                try{
                   let weathers = await fetch(`api/weather/temperature?start=${ this.options.startDate }&end=${ this.options.endDate }&state=${ this.options.state }&city=${ this.options.city }`).then(res=>res.json());
                   this.$emit("updateData",weathers);
                   this.processing = false;
                }
                catch(err){
                    console.log(err);
                    this.processing = false;
                }
                
            },

            cancelTemp(){
                this.$emit("closeTemp");
                this.options.state = "";
                this.options.city = "";
                this.options.startDate = "";
                this.options.endDate = "";
            }
        }
    }
</script>