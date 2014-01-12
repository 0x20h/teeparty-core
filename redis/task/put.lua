--[[
Push a task to the requested channel and register
the task.

KEYS: 
    - the channel
    - task key.
    - pending set key
ARGS: 
    - json-encoded task.
    - json-encoded schedule.
]]--

local channel, task_key, pending_set_key, task, schedule =
	KEYS[1], KEYS[2], KEYS[3], ARGV[1], ARGV[2]

-- store task
redis.call('hmset', task_key, 'task', task, 'channel', channel)
-- push task to channel
redis.call('lpush', channel, task_key)
return 1
