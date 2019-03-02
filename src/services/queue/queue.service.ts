import { OnInit, Service } from '@tsed/di';
import * as IORedis from 'ioredis';
import { RedisService } from '../redis.service';
import { IJob } from './interfaces';

@Service()
export class QueueService implements OnInit {
  private _redis: IORedis.Redis;

  constructor(private redisService: RedisService) {
  }

  public $onInit(): Promise<any> | void {
    this._redis = this.redisService.getClient();
  }

  public enQueue(job: IJob) {
    job.timestamp = (new Date()).valueOf();

    const key = `job:${job.queue}:${job.priority}`;
    const value = JSON.stringify(job);

    console.log({
      key,
      value
    });

    // @ts-ignore
    return this._redis.zadd(key, job.priority, value);
  }


}
