/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

const moment = require('moment-timezone');
require('moment/locale/es');
moment.locale('ru');
moment.tz.setDefault('UTC')
window.Vue = require('vue');
window.io = require('socket.io-client');
Vue.use(require('vue-moment'), {
    moment
});
window.socket = io(':6001');
/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

Vue.component('chat-component', require('./components/ChatComponent.vue').default);
// Vue.component('newmessage-component', require('./components/NewMessageComponent.vue').default);


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
//
const app = new Vue({
    el: '#app',
    data: {
        message: 'Hello Vue!'
    }
});


$('#role').on('load change', function(e){
    console.log(this.value);
    if (this.value == 2){
        $('#specialty').show(500);
    }else{
        $('#specialty').hide(500);
    }
});
