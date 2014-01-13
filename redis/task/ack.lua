--[[
store task results.

KEYS: 
ARGS:
 - key prefix
 - worker_id
 - task_id
 - json encoded task result
]]--

local prefix, worker_id, task_id, json = ARGV[1], ARGV[2], ARGV[3], ARGV[4]

local task_key = prefix .. 'task.' .. task_id
local result_key = prefix .. 'result.' .. task_id
local worker_key = prefix .. 'worker.' .. worker_id

-- get the task
local task = cjson.decode(redis.call('hget', task_key, 'task'))
-- store result as # of tries under result hash
redis.call('hset', result_key, task.meta.tries, json)
-- remove task as current item from worker
redis.call('hdel', worker_key, 'current_task')

-- notify (possible listening) parties
-- @TODO reconsider if clients should just poll. Not sure for now but
-- a blocking variant seems so alluring
redis.call('lpush', result_key .. '.notify', task.meta.tries)
redis.call('expire', result_key .. '.notify', 20)
