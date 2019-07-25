const url = 'amg.loc';
var request = require('request'),
    io = require('socket.io')(6001, {
        origins: url+':*'
    }),
    Redis = require('ioredis'),
    redis = new Redis();
var users = {};
var firstUser = true;

// проверяем авторизованный поьзватель или нет
// проверяем первый пользователь, если да то всех оффлайн
io.use(function (socket, next) {
    let option = {}
    if (firstUser){
        option = {
            firstUser: true,
        };
    }
    request.get({
        url: 'http://' + url + '/ws/check-auth',
        headers: {cookie: socket.request.headers.cookie},
        qs: option,
        json: true
    }, function (error, response, json) {
        socket.user_id = json.user_id
        users[socket.user_id] = socket.id;
        if (json.auth){
            firstUser = false;
        }
        return json.auth ? next() : next(new Error('Auth error'));
    });

});

io.on('connection', function (socket) {
    io
        .emit('userList', users);
    socket.on('actionUser', function (channel, data) {
        //console.log('actionUser ' + channel)
        io
            .to(channel + ':actionUser')
            .emit(channel + ':actionUser', {user_id : socket.user_id, chat_id:data});
    });
    // проверка доступа к  каналу
    socket.on('subscribe', function (channel) {
        //console.log(io)
        request.get({
            url: 'http://' + url + '/ws/check-sub/' + channel,
            headers: {cookie: socket.request.headers.cookie},
            json: true
        }, function (error, response, json) {
            if (json.access) {
                socket.join(channel);
                socket.join(channel + ':actionUser');
            } else {
                return;
            }
        });
    });
    // устанавливаем статус офлайн при отключении
    socket.once('disconnect', function () {
        delete users[socket.user_id];
        io
            .emit('userList', users);
        request.get({
            url: 'http://' + url + '/ws/status-offline/',
            headers: {cookie: socket.request.headers.cookie},
            json: true
        });
        //console.log(users);
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

