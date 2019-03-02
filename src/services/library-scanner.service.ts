import { Service } from '@tsed/di';
import * as klaw from 'klaw';
import { $log } from 'ts-log-debug';
import { LibraryEntity } from '../entities/library.entity';
import { LibraryService } from './library.service';
import { MetadataService } from './metadata.service';
import { MovieMetadata } from './metadata/interfaces';
import { IJob, JobType, Queue, QueuePriority } from './queue/interfaces';
import { QueueService } from './queue/queue.service';

export interface Inode {
  path: string;
  isDir: boolean;
  size: number;
}

export interface DirectoryListing {
  path: string;
  files: Inode[];
}

export interface LibraryScanResult {
  dir: DirectoryListing;
  metadata: MovieMetadata;
}

@Service()
export class LibraryScannerService {

  constructor(private libraryService: LibraryService,
              private metadataService: MetadataService,
              private queueService: QueueService) {
  }

  public async scanLibraryByUuid(uuid: string): Promise<any> {
    const library = await this.libraryService.findByUuid(uuid);

    const job: IJob = {
      name: JobType.LIBRARY_SCANNER_JOB,
      queue: Queue.DEFAULT,
      priority: QueuePriority.HIGH,
      context: library,
    };

    await this.queueService.enQueue(job);

    $log.debug('[LibraryScannerService]: Enqueued new library scan job!');

    return;
  }


  public async getDirectoryListing(library: LibraryEntity): Promise<DirectoryListing[]> {
    const directories = await this.scan(library.path, 0);
    const listings: DirectoryListing[] = [];

    for (const dir of directories) {
      const files = await this.scan(dir.path, 0);

      listings.push({path: dir.path, files});
    }

    return listings;
  }

  private async scan(path: string, depth: number): Promise<Inode[]> {
    return new Promise<Inode[]>(((resolve, reject) => {
      const items: Inode[] = [];

      klaw(path, {depthLimit: depth})
        .on('data', (item) => {
          if (item.path !== path) {
            items.push({
              path: item.path,
              isDir: item.stats.isDirectory(),
              size: item.stats.size,
            });
          }
        })
        .on('end', () => {
          resolve(items);
        });
    }));
  }
}
