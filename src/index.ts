process.on('uncaughtException', function (exception) {
  console.log(exception); // to see your exception details in the console
  // if you are on production, maybe you can send the exception details to your
  // email as well ?
});

process.on('unhandledRejection', (reason, p) => {
  console.log('Unhandled Rejection at: Promise ', p, ' reason: ', reason);
  // application specific logging, throwing an error, or other logic here
});

import { $log } from 'ts-log-debug';
import { Server } from './Server';

$log.name = 'LibertyAPI';

$log.appenders.set('file-info', {
  type: 'file',
  filename: 'info.log',
  maxLogSize: 10485760,
});

new Server().start()
  .catch((er) => {
    $log.error(er);
  });
