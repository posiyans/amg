<template>
    <div>
        <div class="card anyClass" id="scroll">
            <div class="list-group">
                <div v-for="item in allMessage"  class="list-group-item list-group-item-action" :class="item.user_id | masterFilter(user)">{{ item.user_id}} {{ item.created_at }} {{ item.text }}</div>
<!--                <a href="#" class="list-group-item list-group-item-action list-group-item-info">This is a info list-->
<!--                    group item</a>-->
<!--                <a href="#" class="list-group-item list-group-item-action list-group-item-light">This is a light list-->
<!--                    group item</a>-->
            </div>
        </div>
        <div class="input-group mb-3">
            <input type="text" class="form-control" placeholder="Введите текст" aria-describedby="basic-addon2" v-model="message">
            <div class="input-group-append">
                <button class="btn btn-primary" @click='sendMesage' @keydown="actionUser" type="button">Отправить</button>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: [
            'room',
            'user'
        ],
        filters: {
            masterFilter(user_id, user) {
                if (user_id == user.id){
                    return 'list-group-item-light'
                }else{
                    return 'list-group-item-info'
                }
            }
        },
        data() {
            return {
                message: '',
                allMessage: [{}

                ]
            }
        },
        mounted() {
            console.log(this.user.id);
            var socket = io(':6001'),
                channel = "laravel_database_new-message.1:App\\Events\\NewMessage";
            socket.on('connect', function() {
                console.log('connected');
                axios.get('/get-message/1').then(response =>{
                    this.allMessage = response.data.data;
                    let w = document.querySelector(".anyClass");
                    console.log(response.data.data);
                    $('#scroll').animate({scrollTop:response.data.data.length*50}, 'slow');
                });
            }.bind(this));
            socket.on('error', function(error) {
                console.warn('Error', error);
            });

            socket.on('*', function(message) {
                console.info(message);
            });
            socket.on("laravel_database_new-message.1:App\\Events\\NewMessage", function (data){
                this.allMessage.push({ text:  data.text, user_id: data.user_id, created_at: data.created_at})
                $('#scroll').animate({scrollTop:this.allMessage.length*50}, 'slow');
            }.bind(this))
            console.log('Component mounted.')
            //console.log(this.user)
        },
        methods:{
           sendMesage(){
               if (this.message != '') {
                   axios.post('/send-message', {message: this.message, room_id: this.room.id}).then((response)=>{
                       //console.log(response)
                   });
                   this.message = ''
               }
           },
            actionUser(){

            }

        }
    }
</script>
<style>
    .anyClass {
        height:500px;
        overflow-y: scroll;
    }
</style>