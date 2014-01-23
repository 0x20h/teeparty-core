--[[
Pop a pending item from the requested channel, increase the number
of tries and register the item for the given worker.

KEYS: 
 - key prefix
 - the channel
 - worker_id
 - now (unix_timestamp)
]]--

local prefix, channel, worker_id, now = ARGV[1], ARGV[2], ARGV[3], ARGV[4]
local task_id = redis.call('rpop', prefix .. 'channel.' .. channel)

if task_id then
    local task_key = prefix .. 'task.' .. task_id
    local worker_key = prefix .. 'worker.' .. worker_id
    
    -- load task 
    local json = redis.call('hget', task_key, 'task')
    local task = cjson.decode(json)

    -- increase tries / update task
    task.meta.tries = tonumber(task.meta.tries or "0") + 1
    json = cjson.encode(task)
    redis.call('hset', task_key, 'task', json)
    
    -- register that worker processes task
    redis.call(
        'hmset', worker_key,
        'current_task', task_id,
        'current_task_start', now
    )

    return json
end

return 0
