var fs = require('fs'),
    events = require('events'),
    path = require('path'),
    crypto = require('crypto'),
    shasums = {}

module.exports.script = script
module.exports.sha1 = sha1
script.prototype.__proto__ = events.EventEmitter.prototype

function script(file, fn) {
  if (!(this instanceof script)) {
      return new script(file, fn)
  }

  var self = this
  events.EventEmitter.call(this)
  var base = path.resolve([__dirname, '..', '..', '..', 'redis'].join('/'))

  fs.readFile([base, file + '.lua'].join('/'), function(err, data) {
    if (err) {
      self.emit('error', err)
    } else {
        fn(data)
    }
  })
}

/**
 * return sha1 sum for the given script
 *
 * @param scriptfile lua script name
 * @param fn callback(sha1sum)
 */
function sha1(file, fn) {
  if (shasums[file]) {
    return fn(shasums[file])
  }

  script(file, function(lua_source) {
    var h = crypto.createHash('sha1')
    shasums[file] = h.update(lua_source).digest('hex')
    fn(shasums[file])
  })
}
