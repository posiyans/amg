var request = require('request'),
    io = require('socket.io')(6001, {
        origins : 'amg.loc:*'
    }),
    Redis = require('ioredis'),
    redis = new Redis();

// io.use(function(socket, next) {
//
//     request.get({
//         url : 'http://amg.loc/ws/check-auth',
//         headers : {cookie : socket.request.headers.cookie},
//         json : true
//     }, function(error, response, json) {
//         console.log('check-auth_');
//         console.log(json);
//         console.log('_check-auth');
//         return json.auth ? next() : next(new Error('Auth error'));
//     });
//
// });

io.on('connection', function(socket) {
    console.log('connection_');
})
// io.on('connection', function(socket) {
//     console.log('connection_');
//     socket.on('subscribe', function(channel) {
//         console.log('I want to subscribe on:', channel);
//
//         request.get({
//             url : 'http://amg.loc/ws/check-sub/' + channel,
//             headers : {cookie : socket.request.headers.cookie},
//             json : true
//         }, function(error, response, json) {
//             if(json.can) {
//                 socket.join(channel, function(error) {
//                     socket.send('Join to ' + channel);
//                 });
//                 return;
//             }
//         });
//
//     });
//
// });



redis.psubscribe('laravel_database_new-message.*', function(error, count) {
    console.log('psubscribe');
    console.log(error);
    console.log(count);
});

redis.on('pmessage', function(pattern, channel, message) {
     console.log('pmessage');
     console.log('pattern: '+pattern);
    console.log('channel: ' + channel);
    console.log('message: '+ message);
    message = JSON.parse(message);
    console.log('message_json: '+ message);
     console.log(channel + ':'+ message.event);
     io.emit(channel + ':' + message.event, message.data);
    // io
    //     //.to(channel + ':' + message.event)
    //     .emit(channel + ':sd' + message.event, message.data.message);
    // // channel:message.event
});