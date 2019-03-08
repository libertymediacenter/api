import { AfterRoutesInit, Service } from '@tsed/common';
import { TypeORMService } from '@tsed/typeorm';
import * as IORedis from 'ioredis';
import * as _ from 'lodash';
import { $log } from 'ts-log-debug';
import { Repository } from 'typeorm';

import { LibraryEntity, LibraryType } from '../../../../entities/library.entity';
import { MovieEntity } from '../../../../entities/media/movie/movie.entity';
import { SeriesEntity } from '../../../../entities/media/tv/series.entity';
import { DirectoryListing, LibraryScannerService } from '../../../library-scanner.service';
import { LibraryService } from '../../../library.service';
import { MetadataService } from '../../../metadata.service';
import { MetadataOptions } from '../../../metadata/providers/provider.interface';
import { RedisService } from '../../../redis.service';
import { IJob } from '../../interfaces';

@Service()
export class LibraryScannerJob implements AfterRoutesInit {
  private _redis: IORedis.Redis;

  private _movieRepo: Repository<MovieEntity>;
  private _seriesRepo: Repository<SeriesEntity>;
  private _libraryRepo: Repository<LibraryEntity>;
  private _library: LibraryEntity;

  private _hrtime: [number, number];

  constructor(private _typeOrmService: TypeORMService,
              private _redisService: RedisService,
              private _libraryScanner: LibraryScannerService,
              private _libraryService: LibraryService,
              private _metadataService: MetadataService) {
  }

  public $afterRoutesInit(): void | Promise<any> {
    this._redis = this._redisService.getClient();

    const connection = this._typeOrmService.get();

    this._libraryRepo = connection.getRepository(LibraryEntity);
    this._seriesRepo = connection.getRepository(SeriesEntity);
    this._movieRepo = connection.getRepository(MovieEntity);

    this.poll()
      .then()
      .catch();
  }

  private async poll() {
    // @ts-ignore
    const res = await this._redis.zpopmin(`job:default:2`);
    if (!res[0]) {
      return;
    }

    try {
      let job = JSON.parse(res[0]) as IJob;

      await this.task(job.context);
    } catch (e) {
      $log.error(e);
    }
  }

  public async task(context: object): Promise<void> {
    this._library = context as LibraryEntity;

    return new Promise<void>(((resolve, reject) => {
      this.main()
        .then(() => {
          this.destroy();

          resolve();
        })
        .catch((e) => {
          $log.error('[LibraryScannerJob]: Error!', e);
          this.destroy();

          reject();
        });
    }));
  }

  private async main() {
    const directoryListing = await this._libraryScanner.getDirectoryListing(this._library);

    for (const [idx, dir] of directoryListing.entries()) {
      this._hrtime = process.hrtime();

      $log.info(`[LibraryScanner](${this._library.title}): Scanning ${idx + 1} of ${directoryListing.length}`);

      const options = await this.getItemOptions(dir.path);
      if (options) {
        await this.fetchMetadata(dir, options);
      }

      this.logTime(`Scanned ${idx + 1} of ${directoryListing.length}`);
    }

    this.logTime(`Finished scanning ${this._library.title}`);
  }

  private async getItemOptions(dir: string): Promise<MetadataOptions> {
    switch (this._library.type) {
      case LibraryType.Movie:
        return this.getMovieOptions(dir);

      case LibraryType.Series:
        return this.getSeriesOptions(dir);
      default:
        return null;
    }
  }

  private async getSeriesOptions(dir: string) {
    //@ts-ignore
    let options: MetadataOptions = {type: this._library.type, fetchPoster: true, fetchBackdrop: true};

    try {
      const series = await this._seriesRepo.findOneOrFail({
        where: {path: dir},
        cache: 60000,
      });

      if (series.poster) {
        options.fetchPoster = false;
      }

      if (series.backdrop) {
        options.fetchBackdrop = false;
      }

      if (_.has(series, ['title', 'startYear', 'runtime', 'tagline', 'plot', 'imdbId', 'theMovieDbId'])) {
        return null;
      }
    } catch (e) {
      // Series does not exist, proceed with all options.
    }

    return options;
  }

  private async getMovieOptions(dir: string) {
    //@ts-ignore
    let options: MetadataOptions = {type: this._library.type, fetchPoster: true, fetchBackdrop: true};

    try {
      const movie = await this._movieRepo.findOneOrFail({
        where: {path: dir},
        cache: 60000,
      });

      if (movie.poster) {
        options.fetchPoster = false;
      }

      if (movie.backdrop) {
        options.fetchBackdrop = false;
      }

      if (_.has(movie, ['title', 'year', 'runtime', 'tagline', 'plot', 'imdbId', 'theMovieDbId'])) {
        return null;
      }
    } catch (e) {
      // Movie does not exist, proceed with all options.
    }

    return options;
  }

  private async fetchMetadata(dir: DirectoryListing, options: MetadataOptions): Promise<void> {
    const dirPathArr = dir.path.split('/');
    let dirName = dirPathArr[dirPathArr.length - 1];

    try {
      const metadata = await this._metadataService.getByTitle(dirName, options);

      if (metadata) {
        await this._libraryService.addItem(this._library, {
          dir,
          metadata,
        });
      }
    } catch (e) {
      $log.error(`Error while fetching metadata for ${dirName}`, e);
    }
  }

  private logTime(msg: string) {
    const NS_PER_SEC = 1e9;
    const MS_PER_NS = 1e-6;
    const diff = process.hrtime(this._hrtime);
    const ms = (diff[0] * NS_PER_SEC + diff[1]) * MS_PER_NS;

    $log.info(`[LibraryScanner]: ${msg} | took ${ms.toFixed(0)} ms`);
  }

  private destroy() {

  }
}
