module.exports = {
    'name' : 'Project',
    'api'  : {
        'hostname' : '%HOSTNAME%',
        'port'     : 80,
        'secure'   : false
    },
    'oauth2' : {
        'type'         : 'authorization-code',
        'hostname'     : '%HOSTNAME%',
        'secure'       : false,
        'authorizeUrl' : '/oauth/authorize',
        'tokenUrl'     : '/oauth/token',
        'tokenParam'   : 'Bearer'
    },
    'resources' : [
        require('./application/oauth'),
        require('./application/users')
    ]
};
