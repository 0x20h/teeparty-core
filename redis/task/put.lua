--[[
Push a task to the requested channel and register
the task.

KEYS: 

ARGS: 
    - key prefix
    - the channel
    - task id.
    - json-encoded task.
]]--

local prefix, channel, task_id, task = 
    ARGV[1], ARGV[2], ARGV[3], ARGV[4]

local task_key = prefix .. 'task.' .. task_id

-- store task
redis.call('hmset', task_key, 'task', task, 'channel', channel)
-- push task to channel
redis.call('lpush', prefix .. 'channel.' .. channel, task_id)
return 1
