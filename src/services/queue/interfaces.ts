export enum QueuePriority {
  LOW = 0,
  MEDIUM,
  HIGH,
}

export enum Queue {
  DEFAULT = 'default',
}

export enum JobType {
  LIBRARY_SCANNER_JOB = 'LibraryScannerJob',
}

export interface IJob {
  name: JobType;
  queue: Queue;
  priority: QueuePriority;
  timestamp?: number;
  context: object;
}
