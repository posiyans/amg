<template>
    <div>
        <div class="container">
            <div class="messaging">
                <div class="inbox_msg">
                    <div class="inbox_people">
                        <div class="headind_srch">
                            <div class="recent_heading">
                                <h4>Собеседники</h4>
                            </div>
                            <div class="srch_bar">
                                <div class="stylish-input-group">
                                    <input type="text" class="search-bar" placeholder="Поиск">
                                    <span class="input-group-addon">
                                     <button type="button"> <i class="fa fa-search" aria-hidden="true"></i> </button>
                                </span>
                                </div>
                            </div>
                            <div class="inbox_chat">
                                <div v-for="chat in chats" class="chat_list" @click="setActiveChat(chat.id)"
                                     :class="chat.id | chatActiveFilter(chatActive)">
                                    <div class="chat_people">
                                        <div class="chat_img"><img
                                                src="https://ptetutorials.com/images/user-profile.png"
                                                alt="sunil">
                                            <span v-if="userNoReadMessage[chat.id] > 0" class="chat_no_read">
                                                    {{ userNoReadMessage[chat.id] }}
                                                </span>
                                        </div>
                                        <div class="chat_ib">
                                            <h5>
                                                {{ chat.collocutor.surname }}
                                                {{ chat.collocutor.name }}
                                                {{chat.collocutor.patronymic }}
                                                <span class="chat_date">
                                                    {{ chat.collocutor.email }}
                                                </span>
                                            </h5>
                                            <p style="position: relative;">
                                                {{ userStatus[chat.collocutor.id] }} {{isActive[chat.id]}}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mesgs" @mousemove="move">
                        <div class="msg_history" ref="block">
                            <transition-group name="list" tag="div">
                                <div v-if="allMessage" v-for="message in allMessage" v-bind:key="message.id"
                                     :class="message.user_id |  masterFilter(user)">
                                    <div v-if="message.user_id != user.id" class="incoming_msg_img"><img
                                            src="https://ptetutorials.com/images/user-profile.png"
                                            alt="sunil"></div>
                                    <div :class="message.user_id |  masterMsgFilter(user)">
                                        <div :class="message.user_id |  masterResFilter(user)">
                                            <p>{{ message.text }}</p>
                                            <span class="time_date"> {{ message.created_at | moment('timezone', timeZone, "H:mm:ss,  Do MMMM YYYY") }}</span>
                                        </div>
                                    </div>
                                </div>
                            </transition-group>
                        </div>
                        <div v-if="chatActive" class="type_msg">
                            <div class="input_msg_write">
                                <input type="text" class="write_msg" placeholder="Введите сообщение"
                                       v-on:keydown="actionUser"
                                       v-model="message">
                                <button class="msg_send_btn" @click="sendMesage()" type="button"><i
                                        class="fa fa-paper-plane-o"
                                        aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                        <div v-else class="type_msg">
                            <div class="input_msg_write">
                                <--Выберите чат
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: [
            'user'
        ],
        filters: {
            masterFilter(user_id, user) {
                if (user_id == user.id) {
                    return 'outgoing_msg'
                } else {
                    return 'incoming_msg'
                }
            },
            masterMsgFilter(user_id, user) {
                if (user_id == user.id) {
                    return ''
                } else {
                    return 'received_msg'
                }
            },

            masterResFilter(user_id, user) {
                if (user_id == user.id) {
                    return 'sent_msg'
                } else {
                    return 'received_withd_msg'
                }
            },
            nameFilter(user_id, user) {
                if (user_id == user.id) {
                    return 'я'
                } else {
                    return 'собеседник'
                }
            },
            chatActiveFilter(user_id, chatActive) {
                if (chatActive === user_id) {
                    return 'active_chat'
                }
            },
            userOnlineFilter() {
                return '';
            }
        },
        data() {
            return {
                message: '',
                getMessage: false,
                allMessage: [],
                isActive: {},
                typingTimer: false,
                readChatTimer: false,
                chats: {},
                chatActive: false,
                onlineUser: [],
                timeZone: this.$moment.tz.guess(true),
                userStatus: {},
                userNoReadMessage: {},
                countReadMessage: 0

            }
        },
        mounted() {
            socket.on('userList', function (data) {
                this.onlineUser = [];
                for (let user in data) {
                    this.onlineUser[user] = 'online';
                }
                this.setUserStatus();
                //this.getUserList();
            }.bind(this));
            socket.on("laravel_database_new-message-user." + this.user.id + ":userMessage", function (data) {
                //console.log(data.ticket_id + '___' +data.count);
                //this.userNoReadMessage[data.ticket_id] ++;
                //console.log('countReadMessage ' + this.userNoReadMessage);
                //this.userNoReadMessage
                this.userNoReadMessage[data.ticket_id] ++
                //this.userNoReadMessage = Object.assign({}, (this.userNoReadMessage, {[data.ticket_id]: this.userNoReadMessage[data.ticket_id] ++}));
                // if (!this.url_chat || this.id_chat != data.ticket_id){
                //     this.showMessage = true;
                //     setTimeout(() => {
                //         this.showMessage = false;
                //     }, 5000);
                // }

            }.bind(this));
            this.getUserList();
        },
        methods: {
            move() {
                if (this.chatActive && (this.countReadMessage < this.allMessage.length)) {
                    if (!this.readChatTimer) {
                        this.readChatTimer = setTimeout(() => {
                            //console.log('есть непрочитанное');
                            //console.log(this.allMessage.length - this.countReadMessage);
                            this.countReadMessage = this.allMessage.length;
                            this.readChatTimer = false;
                            this.setActiveChat(this.chatActive);
                        }, 5000);
                    }

                }
            },
            getUserList() {
                axios.get('/get-user-list/').then(response => {
                    this.chats = response.data.userList;
                    this.setUserStatus();
                    this.setOnUserActive();
                });
            },
            sendMesage() {
                if (this.message != '') {
                    axios.post('/send-message', {message: this.message, room_id: this.chatActive}).then((response) => {
                        //console.log(response)
                    });
                    this.message = '';
                }
            },
            actionUser() {
                //console.log(this.message);
                socket.emit("actionUser", "laravel_database_new-message-chat." + this.chatActive + ":userMessage", this.chatActive);
            },
            setOnUserActive() {
                for (let chat in this.chats) {
                    socket.emit("subscribe", "laravel_database_new-message-chat." + this.chats[chat].id + ":userMessage");
                    socket.on("laravel_database_new-message-chat." + this.chats[chat].id + ":userMessage:actionUser", function (data) {
                        if (data.user_id != this.user.id) {
                            this.isActive = Object.assign({}, (this.isActive, {[data.chat_id]: 'печатает...'}));
                            if (this.typingTimer) clearTimeout(this.typingTimer);
                            this.typingTimer = setTimeout(() => {
                                this.isActive = Object.assign({}, (this.isActive, {[data.chat_id]: ''}));
                            }, 2000);
                        }
                    }.bind(this));
                }
            },
            setActiveChat(chat_id) {
                this.chatActive = chat_id;
                axios.get('/get-message/' + this.chatActive).then(response => {
                    this.allMessage = response.data.data;
                    this.getMessage = true;
                    this.countReadMessage = response.data.data.length;
                    this.userNoReadMessage[chat_id] = 0;
                });
                socket.emit("subscribe", "laravel_database_new-message-chat." + this.chatActive + ":userMessage");
                socket.on("laravel_database_new-message-chat." + this.chatActive + ":userMessage", function (data) {
                    // this.allMessage = Object.assign({}, (this.allMessage, data));
                    //this.allMessage.push({id: data.id, text: data.text, user_id: data.user_id, created_at: data.created_at})
                    this.allMessage.push(data);
                    this.countReadMessage = this.allMessage.length;
                    //console.log(this.allMessage);
                    this.isActive = false;
                }.bind(this))
            },
            setUserStatus() {
                let status = {};
                let read = {};
                for (let chat in this.chats) {
                    read[this.chats[chat].id] = this.chats[chat].noReadMessage;
                   // console.log('chat_id: ' + this.chats[chat].collocutor.id);
                    if (this.userStatus[this.chats[chat].collocutor.id] != "online") {
                        status[this.chats[chat].collocutor.id] = this.$moment(this.chats[chat].collocutor.updated_at).fromNow();
                    } else {
                        status[this.chats[chat].collocutor.id] = 'offline';
                    }
                }
                for (let user in this.onlineUser) {
                    status[user] = "online";
                }
                this.userStatus = status;
                this.userNoReadMessage = read;
            }
        },
        computed: {
            userStatgus: function () {
                let status = [];
                for (let chat in this.chats) {
                    //console.log('chat_id: ' + this.chats[chat].collocutor.id);
                    status[this.chats[chat].collocutor.id] = this.$moment(this.chats[chat].collocutor.updated_at).fromNow();
                }
                for (let user in this.onlineUser) {
                    status[user] = 'online';
                }
                return status;
            },
        },
        watch: {
            allMessage() {
                setTimeout(() => {
                    this.$refs.block.scrollTop = this.$refs.block.scrollHeight;
                }, 100);
            }
        }
    }
</script>
<style>
    .list-enter-active, .list-leave-active {
        transition: all 2s;
    }

    .list-enter, .list-leave-to /* .list-leave-active до версии 2.1.8 */
    {
        opacity: 0;
        transform: translateX(120px);
    }


    .chat_no_read {
        /*float: right;*/
        background: red;
        /*color: black;*/
        /*width: 25px;*/
        /*height: 25px;*/
        /*text-align: center;*/
        border-radius: 2px;

        position: absolute;
        top: 0px;
        right: 0px;
        text-align: center;
        font-size: 9px;
        padding: 2px 3px;
        line-height: .9;
    }

    .container {
        max-width: 1170px;
        margin: auto;
    }

    img {
        max-width: 100%;
    }

    .inbox_people {
        background: #f8f8f8 none repeat scroll 0 0;
        float: left;
        overflow: hidden;
        width: 40%;
        border-right: 1px solid #c4c4c4;
    }

    .inbox_msg {
        border: 1px solid #c4c4c4;
        clear: both;
        overflow: hidden;
    }

    .top_spac {
        margin: 20px 0 0;
    }


    .recent_heading {
        float: left;
        width: 40%;
    }

    .srch_bar {
        display: inline-block;
        text-align: right;
        width: 60%;
    }

    .headind_srch {
        padding: 10px 29px 10px 20px;
        overflow: hidden;
        border-bottom: 1px solid #c4c4c4;
    }

    .recent_heading h4 {
        color: #05728f;
        font-size: 21px;
        margin: auto;
    }

    .srch_bar input {
        border: 1px solid #cdcdcd;
        border-width: 0 0 1px 0;
        width: 80%;
        padding: 2px 0 4px 6px;
        background: none;
    }

    .srch_bar .input-group-addon button {
        background: rgba(0, 0, 0, 0) none repeat scroll 0 0;
        border: medium none;
        padding: 0;
        color: #707070;
        font-size: 18px;
    }

    .srch_bar .input-group-addon {
        margin: 0 0 0 -27px;
    }

    .chat_ib h5 {
        font-size: 15px;
        color: #464646;
        margin: 0 0 8px 0;
    }

    .chat_ib h5 span {
        font-size: 13px;
        float: right;
    }

    .chat_ib p {
        font-size: 14px;
        color: #989898;
        margin: auto
    }

    .chat_img {
        float: left;
        width: 11%;
        position: relative;
    }

    .chat_ib {
        float: left;
        padding: 0 0 0 15px;
        width: 88%;
    }

    .chat_people {
        overflow: hidden;
        clear: both;
    }

    .chat_list {
        border-bottom: 1px solid #c4c4c4;
        margin: 0;
        padding: 18px 16px 10px;
    }

    .inbox_chat {
        height: 550px;
        overflow-y: scroll;
    }

    .active_chat {
        background: #ebebeb;
    }

    .incoming_msg_img {
        display: inline-block;
        width: 6%;
    }

    .received_msg {
        display: inline-block;
        padding: 0 0 0 10px;
        vertical-align: top;
        width: 92%;
    }

    .received_withd_msg p {
        background: #ebebeb none repeat scroll 0 0;
        border-radius: 3px;
        color: #646464;
        font-size: 14px;
        margin: 0;
        padding: 5px 10px 5px 12px;
        width: 100%;
    }

    .time_date {
        color: #747474;
        display: block;
        font-size: 12px;
        margin: 8px 0 0;
    }

    .received_withd_msg {
        width: 57%;
    }

    .mesgs {
        float: left;
        padding: 30px 15px 0 25px;
        width: 60%;
    }

    .sent_msg p {
        background: #05728f none repeat scroll 0 0;
        border-radius: 3px;
        font-size: 14px;
        margin: 0;
        color: #fff;
        padding: 5px 10px 5px 12px;
        width: 100%;
    }

    .outgoing_msg {
        overflow: hidden;
        margin: 26px 0 26px;
    }

    .sent_msg {
        float: right;
        width: 46%;
    }

    .input_msg_write input {
        background: rgba(0, 0, 0, 0) none repeat scroll 0 0;
        border: medium none;
        color: #4c4c4c;
        font-size: 15px;
        min-height: 48px;
        width: 100%;
    }

    .type_msg {
        border-top: 1px solid #c4c4c4;
        position: relative;
    }

    .msg_send_btn {
        background: #05728f none repeat scroll 0 0;
        border: medium none;
        border-radius: 50%;
        color: #fff;
        cursor: pointer;
        font-size: 17px;
        height: 33px;
        position: absolute;
        right: 0;
        top: 11px;
        width: 33px;
    }

    .messaging {
        padding: 0 0 50px 0;
    }

    .msg_history {
        height: 516px;
        overflow-y: auto;
    }
</style>