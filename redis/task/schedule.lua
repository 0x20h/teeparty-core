--[[
Schedule a task at the specified execution time.

KEYS: 
    - the channel
    - task key.
    - schedule_zset_key
ARGS: 
    - json-encoded task.
    - json-encoded schedule.
	- next execution time (as unix timestamp)
]]--

local channel, task_key, schedule_set_key, task, schedule, next_execution =
	KEYS[1], KEYS[2], KEYS[3], ARGV[1], ARGV[2]

-- store task
redis.call('hmset', task_key, 'task', task, 'channel', channel)
-- register task in the schedule
redis.call('zadd', schedule_set_key, next_execution, task_key)
