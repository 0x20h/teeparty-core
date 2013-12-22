--[[
Push a task to the requsted channel and register
the task.

KEYS: 
    - the channel
    - task key.
ARGS: 
    - json-encoded task.
    - json-encoded schedule.
]]--

local channel, task_key, task, schedule = KEYS[1], KEYS[2], ARGV[1], ARGV[2]

-- store task
redis.call('hmset', task_key, 'task', task, 'channel', channel)
-- push task to channel
redis.call('lpush', channel, task_key)
return 1
