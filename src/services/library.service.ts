import { AfterRoutesInit } from '@tsed/common';
import { Service } from '@tsed/di';
import { TypeORMService } from '@tsed/typeorm';
import { AxiosRequestConfig } from 'axios';
import { extension } from 'mime-types';
import * as slug from 'slug';
import { Connection, Repository } from 'typeorm';
import { LibraryEntity, LibraryType } from '../entities/library.entity';
import { MovieEntity } from '../entities/media/movie.entity';
import { EpisodeEntity } from '../entities/media/tv/episode.entity';
import { SeriesEntity } from '../entities/media/tv/series.entity';
import { StorageDir, streamToFile } from '../utils/storage.utils';
import { LibraryScanResult } from './library-scanner.service';
import { MovieMetadata } from './metadata/interfaces';
import { getMimeType } from './metadata/providers/fetch-stream';
import { ImageType } from './metadata/providers/provider.interface';
import { $log } from 'ts-log-debug';

@Service()
export class LibraryService implements AfterRoutesInit {
  private _connection: Connection;

  private _libraryRepo: Repository<LibraryEntity>;
  private _movieRepo: Repository<MovieEntity>;

  constructor(private typeOrmService: TypeORMService) {
  }

  public $afterRoutesInit(): void | Promise<any> {
    this._connection = this.typeOrmService.get();
    this._libraryRepo = this._connection.manager.getRepository(LibraryEntity);
    this._movieRepo = this._connection.manager.getRepository(MovieEntity);
  }

  public async findAll(): Promise<[LibraryEntity[], number]> {
    return this._libraryRepo.findAndCount();
  }

  public async findByType(type: LibraryType): Promise<LibraryEntity[]> {
    return this._libraryRepo.find({where: {type}});
  }

  public async findByUuid(uuid: string): Promise<LibraryEntity> {
    return this._libraryRepo.findOneOrFail({where: {uuid}});
  }

  public async findBySlug(slug: string): Promise<LibraryEntity> {
    return this._libraryRepo.findOneOrFail({where: {slug}});
  }

  public async create(data: LibraryEntity): Promise<LibraryEntity> {
    return this._libraryRepo.save(data);
  }

  public async addItem(library: LibraryEntity, data: LibraryScanResult): Promise<MovieEntity | SeriesEntity | EpisodeEntity> {
    switch (library.type) {
      case LibraryType.Movie:
        return await this.createMovie(data);
      default:
        throw new Error('Library type unknown!');
    }
  }

  public async createMovie(data: LibraryScanResult): Promise<MovieEntity> {
    let movieEntity;

    try {
      movieEntity = await this._movieRepo.findOneOrFail({where: {path: data.dir.path}});
    } catch (e) {
      movieEntity = new MovieEntity();
    }

    const metadata = data.metadata as MovieMetadata;

    movieEntity.title = metadata.title;
    movieEntity.slug = slug(`${metadata.title} ${metadata.year}`, {
      lower: true,
      remove: /[*+~.()'"!:@]/g,
    });
    movieEntity.path = data.dir.path;
    movieEntity.runtime = metadata.runtime;
    movieEntity.plot = metadata.plot;
    movieEntity.imdbId = metadata.imdbId;
    movieEntity.theMovieDbId = metadata.theMovieDbId;
    movieEntity.year = metadata.year;
    movieEntity.tagline = metadata.tagline;

    movieEntity = await this._movieRepo.save(movieEntity);

    if (data.metadata.images && data.metadata.images.length > 0) {
      const images = data.metadata.images;

      const download = async (type: string, ext: string, request: AxiosRequestConfig) => {
        return streamToFile(`${movieEntity.uuid}-${type}.${ext}`, StorageDir.IMAGES, request);
      };

      for (const image of images) {
        let mimeType;

        try {
          mimeType = await getMimeType(image.request);
        } catch (e) {
          $log.error('[LibraryService]: Could not request mimeType!', {
            item: data.metadata.title,
            imageType: image.type,
            imageUrl: image.request.url,
            errorType: e.name,
          });

          return;
        }

        const ext = extension(mimeType);

        if (!ext) {
          return;
        }

        if (image.type === ImageType.POSTER && !movieEntity.poster) {
          try {
            movieEntity.poster = await download('poster', ext, image.request);
          } catch (e) {
            $log.error('[LibraryService]: Could not download image', e);

            return;
          }
        }

        if (image.type === ImageType.BACKDROP && !movieEntity.backdrop) {
          try {
            movieEntity.backdrop = await download('backdrop', ext, image.request);
          } catch (e) {
            $log.error('[LibraryService]: Could not download image', e);

            return;
          }
        }
      }

      await this._movieRepo.save(movieEntity);
    }

    return movieEntity;
  }
}
