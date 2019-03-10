import { AfterRoutesInit } from '@tsed/common';
import { Service } from '@tsed/di';
import { TypeORMService } from '@tsed/typeorm';
import { Connection, Repository } from 'typeorm';
import { MovieMediaEntity } from '../entities/media/movie/movie-media.entity';
import { MovieEntity } from '../entities/media/movie/movie.entity';
import { FFProbeResult } from '../interfaces/ffprobe.interfaces';
import { ffprobe } from '../utils/ffprobe';
import { DirectoryListing } from './library-scanner.service';
import { $log } from 'ts-log-debug';

@Service()
export class MovieMediaService implements AfterRoutesInit {
  private _connection: Connection;
  private _movieMediaRepo: Repository<MovieMediaEntity>;

  constructor(private typeOrmService: TypeORMService) {

  }

  public $afterRoutesInit(): void | Promise<any> {
    this._connection = this.typeOrmService.get();
    this._movieMediaRepo = this._connection.getRepository(MovieMediaEntity);
  }

  public async create(dirListing: DirectoryListing, movieEntity: MovieEntity) {

    for (const file of dirListing.files) {
      if (!file.isDir) {
        let probe: FFProbeResult;

        try {
          probe = await ffprobe(file.path);
        } catch (e) {
          $log.error(`[MovieMediaService]: Failed while trying to ffprobe ${file.path}`);
        }

        const entity = new MovieMediaEntity();
        entity.movie = movieEntity;
        entity.path = file.path;
        entity.bitrate = probe.format.bit_rate || null;
        entity.height = probe.streams[0].height || null;
        entity.width = probe.streams[0].width || null;
        entity.size = file.size;

        await this._movieMediaRepo.save(entity);
      }
    }

  }
}