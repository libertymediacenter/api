import { OnInit, Service } from '@tsed/di';
import * as IORedis from 'ioredis';

@Service()
export class RedisService implements OnInit {
  private _redis: IORedis.Redis;

  public $onInit(): Promise<any> | void {
    this._redis = new IORedis({
      host: process.env.REDIS_HOST || '127.0.0.1',
      port: Number(process.env.REDIS_PORT) || 6379,
      password: process.env.REDIS_PASS || '',
    });
  }

  public getClient(): IORedis.Redis {
    return this._redis;
  }

}
