<template>
    <div>
        <div  v-if = "showMessage" id="my-alert" class="alert alert-danger alert-dismissible fade show" role="alert">
            у вас  новое сообщение
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
</template>

<script>
    export default {
        props: [
            'user_id'
        ],
        data() {
            return {
                showMessage: false,
                url_chat: false,
                id_chat: false,
                userNoReadMessage: {},
            }
        },
        created(){
            let uri = window.location.href.split('/');
            if (uri[3] == 'chat'){
                this.url_chat = true;
                this.id_chat = uri[4];
            }

            console.log(this.url_chat);
        },
        mounted() {
            socket.on('connect', function() {
                socket.emit("subscribe", "laravel_database_new-message-user." + this.user_id + ":userMessage");
                socket.on("laravel_database_new-message-user." + this.user_id + ":userMessage", function (data) {
                    //this.userNoReadMessage[data.ticket_id] ++;
                    //console.log(data.ticket_id);
                    // if (!this.url_chat || this.id_chat != data.ticket_id){
                    //     this.showMessage = true;
                    //     setTimeout(() => {
                    //         this.showMessage = false;
                    //     }, 5000);
                    // }

                }.bind(this))
            }.bind(this))
        },
        methods: {
        }
    }
</script>
<style>
</style>