<template>
    <div>
        <div class="card anyClass" id="scroll">
            <div class="list-group">
                <div v-if='getMessage' v-for="item in allMessage"  class="list-group-item list-group-item-action" :class="item.user_id | masterFilter(user)">
                    <b>{{item.user_id | nameFilter(user)}}:</b>
                    {{ item.text }}
                    <span class="time-message">{{ item.created_at }}</span>
                </div>
                <div v-else>
                    идет подключение к серверу....
                </div>
            </div>
        </div>
        <div v-if='getMessage' class="input-group mb-3">
            <input type="text" class="form-control" placeholder="Введите текст" aria-describedby="basic-addon2" v-model="message">
            <div class="input-group-append">
                <button class="btn btn-primary" @click='sendMesage'  type="button">Отправить</button>
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
            },
            nameFilter(user_id, user) {
                if (user_id == user.id){
                    return 'я'
                }else{
                    return 'собеседник'
                }
            }
        },
        data() {
            return {
                message: '',
                getMessage: false,
                allMessage: [{}

                ]
            }
        },
        mounted() {
            socket.on('connect', function() {
                socket.emit("subscribe", "laravel_database_new-message-chat." + this.room.id + ":userMessage");
                axios.get('/get-message/'+ this.room.id).then(response =>{
                    this.allMessage = response.data.data;
                    let w = document.querySelector(".anyClass");
                    $('#scroll').animate({scrollTop:response.data.data.length*50}, 'slow');
                    this.getMessage = true;
                });
            }.bind(this));
            socket.on("laravel_database_new-message-chat." + this.room.id + ":userMessage", function (data){
                this.allMessage.push({ text:  data.text, user_id: data.user_id, created_at: data.created_at})
                $('#scroll').animate({scrollTop:this.allMessage.length*50}, 'slow');
            }.bind(this))
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
        }
    }
</script>
<style>
    .anyClass {
        height:500px;
        overflow-y: scroll;
    }

    .time-message {
        float: right;
    }
</style>