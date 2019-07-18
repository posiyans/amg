var request = require('request'),
    io = require('socket.io')(6001, {
        origins: 'amg.loc:*'
    }),
    Redis = require('ioredis'),
    redis = new Redis();

io.use(function (socket, next) {
    request.get({
        url: 'http://amg.loc/ws/check-auth',
        headers: {cookie: socket.request.headers.cookie},
        json: true
    }, function (error, response, json) {
        return json.auth ? next() : next(new Error('Auth error'));
    });

});


io.on('connection', function (socket) {
    let user_id = '';
    request.get({
        url: 'http://amg.loc/ws/status-online/',
        headers: {cookie: socket.request.headers.cookie},
        json: true
    }, function (error, response, json) {
        user_id = json.user_id
    });

    socket.on('subscribe', function (channel) {
        //     //console.log('joining room', channel);
        request.get({
            url: 'http://amg.loc/ws/check-sub/' + channel,
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
        request.get({
            url: 'http://amg.loc/ws/status-offline/',
            headers: {cookie: socket.request.headers.cookie},
            json: true
        });
    })
})

redis.psubscribe('laravel_database*', function (error, count) {

});

redis.on('pmessage', function (pattern, channel, message) {
    message = JSON.parse(message);
    io
        .to(channel + ':' + message.event)
        .emit(channel + ':' + message.event, message.data);
});