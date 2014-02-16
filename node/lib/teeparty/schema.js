var JaySchema = require('jayschema'),
    validator = new JaySchema,
    fs = require('fs'),
    events = require('events'),
    path = require('path')

module.exports.validate = validate

function validate(schemafile, json, fn) {
  if (!(this instanceof validate)) {
      return new validate(schemafile, json, fn)
  }

  var self = this
  events.EventEmitter.call(this)
  var base = path.resolve([__dirname, '..', '..', '..', 'schema'].join('/'))

  fs.readFile([base, schemafile + '.json'].join('/'), function(err, data) {
    if (err) {
      self.emit('error', err)
    } else {
      var schema = JSON.parse(data.toString())
      validator.validate(json, schema, function(errs) {
        fn(errs)
      })
    }
  })
}

validate.prototype.__proto__ = events.EventEmitter.prototype
