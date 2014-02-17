var assert = require('assert'),
    lua = require('./../../lib/teeparty/lua')

describe('lua.script', function() {
  it('should fail on non-existing script', function(done) {
    lua.removeAllListeners('error')
    lua.script('non-existant', function(source) {
    }).on('error', function(err) {
      done()
    })
  })

  it('should return valid script source', function(done) {
    lua.script('task/get', function(source) {
      assert(source)
      done()
    })
  })
})


describe('lua.sha1', function() {
  it('should emit an error event for non-existing scripts', function(done) {
    lua.removeAllListeners('error')
    lua.on('error', function(err) {
      done()
    })

    lua.sha1('non-existant', function(d) {})
  })

  it('should return valid sha1 sum for a valid script', function(done) {
    lua.sha1('task/get', function(d) {
      assert.ok(/[a-f0-9]{40,40}/.test(d))
      done()
    })
  })
})
