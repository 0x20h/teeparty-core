var fs = require('fs'),
    events = require('events'),
    path = require('path'),
    crypto = require('crypto'),
    shasums = {}

module.exports = lua()
lua.prototype.__proto__ = events.EventEmitter.prototype

function lua() {
  if (!(this instanceof lua)) {
      return new lua()
  }

  events.EventEmitter.call(this)
}

/**
 * return the script source.
 *
 * @param string file The script file (rel. to redis/ directory)
 * @param fn callback(script_source)
 * @return lua
 */

lua.prototype.script = function(file, fn) {
  var base = path.resolve([__dirname, '..', '..', '..', 'redis'].join('/')),
      self = this

  fs.readFile([base, file + '.lua'].join('/'), function(err, data) {
    if (err) {
      self.emit('error', err)
    } else {
        fn(data)
    }
  })

  return this
}

/**
 * return sha1 sum for the given script
 *
 * @param scriptfile lua script name
 * @param fn callback(sha1sum)
 */
lua.prototype.sha1 = function(file, fn) {
  if (shasums[file]) {
    fn(shasums[file])
  } else {
    this.script(file, function(lua_source) {
      var h = crypto.createHash('sha1')
      shasums[file] = h.update(lua_source).digest('hex')
      fn(shasums[file])
    })
  }

  return this
}
