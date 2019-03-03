import * as dotenv from 'dotenv';
import '@tsed/swagger';
import { GlobalAcceptMimesMiddleware, ServerLoader, ServerSettings } from '@tsed/common';
import { Env } from '@tsed/core';
import { $log } from 'ts-log-debug';

import * as cookieParser from 'cookie-parser';
import * as bodyParser from 'body-parser';
import * as compress from 'compression';
import * as methodOverride from 'method-override';
import * as cors from 'cors';
import * as compression from 'compression';
import * as helmet from 'helmet';

import * as slug from 'slug';

const rootDir = __dirname;

dotenv.config({path: `${rootDir}/../.env`});

@ServerSettings({
  rootDir,
  port: process.env.HTTP_PORT || 8080,
  httpsPort: false,
  acceptMimes: ['application/json'],
  env: Env[process.env.NODE_ENV] || Env.DEV,
  mount: {
    '/v1': `${rootDir}/controllers/v1/**/*.{js,ts}`,
    '/': `${rootDir}/controllers/root/**/*.{js,ts}`,
  },
  statics: {
    '/': `${rootDir}/../public`,
  },
  logger: {
    debug: false,
    logRequest: false,
    requestFields: ['reqId', 'method', 'url', 'headers', 'query', 'params', 'duration'],
  },
  typeorm: [
    {
      name: 'default',
      type: 'postgres',
      host: process.env.POSTGRES_HOST || 'localhost',
      port: process.env.POSTGRES_PORT || 5432,
      username: process.env.POSTGRES_USER || 'postgres',
      password: process.env.POSTGRES_PASS || 'postgres',
      database: process.env.POSTGRES_DB || 'postgres',
      synchronize: true,
      logging: false,
      cache: {
        type: 'ioredis',
        options: {
          host: process.env.REDIS_HOST || '127.0.0.1',
          port: process.env.REDIS_PORT || 6379,
          password: process.env.REDIS_PASS || '',
        },
      },
      entities: [
        `${rootDir}/entities/**/*{.ts,.js}`,
      ],
      migrations: [
        `${rootDir}/migrations/*{.ts,.js}`,
      ],
      subscribers: [
        `${rootDir}/subscribers/*{.ts,.js}`,
      ],
      cli: {
        migrationsDir: 'migrations',
      },
    },
  ],
  swagger: {
    path: '/docs',
  },
})
export class Server extends ServerLoader {
  /**
   * This method let you configure the middleware required by your application to works.
   * @returns {Server}
   */
  $onMountingMiddlewares(): void | Promise<any> {
    this
      .use(GlobalAcceptMimesMiddleware)
      .use(compression())
      .use(cors())
      .use(cookieParser())
      .use(compress({}))
      .use(methodOverride())
      .use(bodyParser.json())
      .use(bodyParser.urlencoded({
        extended: true,
      }))
      .use(helmet({
        frameguard: {
          action: 'deny',
        },
        hsts: false,
        referrerPolicy: {
          policy: 'same-origin',
        },
        hidePoweredBy: {
          setTo: 'Liberty',
        },
        noCache: true,
      }));

    return null;
  }

  $onInit(): void | Promise<any> {
    slug.defaults.modes['rfc3986'] = {
      replacement: '-',      // replace spaces with replacement
      symbols: true,         // replace unicode symbols or not
      remove: null,          // (optional) regex to remove characters
      lower: true,           // result in lower case
      charmap: slug.charmap, // replace special characters
      multicharmap: slug.multicharmap, // replace multi-characters
    };

    slug.defaults.mode = 'rfc3986';
  }

  $onReady() {
    $log.debug('Server initialized');
  }

  $onServerInitError(error): any {
    $log.error('Server encounter an error =>', error);
  }
}
