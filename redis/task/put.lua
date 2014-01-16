--[[
Push a task to the requested channel and register
the task.

KEYS: 

ARGS: 
    - key prefix
    - the channel
    - task id.
    - json-encoded task.
    - execution time (OPTIONAL)
]]--

local prefix, channel, task_id, task, execution_time =
    ARGV[1], ARGV[2], ARGV[3], ARGV[4], ARGV[5]

local task_key = prefix .. 'task.' .. task_id

-- store task
redis.call('hmset', task_key, 'task', task, 'channel', channel)
if execution_time then
    -- schedule task for later execution
    redis.call('zadd', prefix .. 'scheduler', execution_time, task_id)
else
    -- push task to channel for ASAP execution
    redis.call('lpush', prefix .. 'channel.' .. channel, task_id)
end

return 1
