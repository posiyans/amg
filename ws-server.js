const url = 'amg.loc';
var request = require('request'),
    io = require('socket.io')(6001, {
        origins: url+':*'
    }),
    Redis = require('ioredis'),
    redis = new Redis();
var users = {};
var firstUser = true;

io.use(function (socket, next) {
    let option = {}
    if (firstUser){
        option = {
            firstUser: true,
        };
        firstUser = false;
    }
    request.get({
        url: 'http://' + url + '/ws/check-auth',
        headers: {cookie: socket.request.headers.cookie},
        qs: option,
        json: true
    }, function (error, response, json) {
        socket.user_id = json.user_id
        users[socket.user_id] = socket.id;
        return json.auth ? next() : next(new Error('Auth error'));
    });

});

io.on('connection', function (socket) {
    socket.on('subscribe', function (channel) {
        request.get({
            url: 'http://' + url + '/ws/check-sub/' + channel,
            headers: {cookie: socket.request.headers.cookie},
            json: true
        }, function (error, response, json) {
            if (json.access) {
                socket.join(channel);
            } else {
                return;
            }
        });
    });

    socket.once('disconnect', function () {
        delete users[socket.user_id];
        request.get({
            url: 'http://' + url + '/ws/status-offline/',
            headers: {cookie: socket.request.headers.cookie},
            json: true
        });
        console.log(users);
    })
});

redis.psubscribe('laravel_database*', function (error, count) {

});

redis.on('pmessage', function (pattern, channel, message) {
    message = JSON.parse(message);
    console.log(message);
    io
        .to(channel + ':' + message.event)
        .emit(channel + ':' + message.event, message.data);
});

// process.on('SIGINT', () => {
//     console.log("Intercepting SIGINT");
//
//     process.exit('SIGINT');
// });