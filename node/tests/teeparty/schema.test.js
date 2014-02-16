var assert = require('assert'),
    schema = require('./../../lib/teeparty/schema')

describe('schema', function() {
  it('should fail on non-existing schema', function(done) {
    schema.validate('non-existant', '', function(err) {
    }).on('error', function(err) {
      done()
    })
  })

  it('should validate a valid task', function(done) {
    var now = new Date

    var task = {
      job: 'someJob',
      meta: {
        id: 'a12fab',
        created: now.toISOString(),
        max_tries: 3,
        tries: 0
      }
    }

    schema.validate('task', task, function(err) {
      if (err) {
        console.error(err)
      } else {
        done()
      }
    })
  })

  it('should report correct error on invalid task', function(done) {
    var task = {
      job: 'foo'
      // meta missing
    }

    schema.validate('task', task, function(err) {
      if (err) {
        assert.equal(err[0].constraintName, 'required')
        assert.equal(err[0].desc, 'missing: meta')
        done()
      }
    })
  })
})
