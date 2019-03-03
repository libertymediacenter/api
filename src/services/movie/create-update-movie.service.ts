import { AfterRoutesInit } from '@tsed/common';
import { Service } from '@tsed/di';
import { TypeORMService } from '@tsed/typeorm';
import { AxiosError, AxiosRequestConfig } from 'axios';
import { extension } from 'mime-types';
import { $log } from 'ts-log-debug';
import { Connection, Repository } from 'typeorm';
import { GenreEntity } from '../../entities/genre.entity';
import { LibraryEntity } from '../../entities/library.entity';
import { MovieCastEntity } from '../../entities/media/movie-cast.entity';
import { MovieEntity } from '../../entities/media/movie.entity';
import { StorageDir, streamToFile } from '../../utils/storage.utils';
import { LibraryScanResult } from '../library-scanner.service';
import { MovieMetadata } from '../metadata/interfaces';
import { getMimeType } from '../metadata/providers/fetch-stream';
import { ImageRequest, ImageType } from '../metadata/providers/provider.interface';
import { TmdbProvider } from '../metadata/providers/tmdb/tmdb.provider';
import { PersonService } from '../person.service';

@Service()
export class CreateUpdateMovieService implements AfterRoutesInit {
  public readonly logPrefix = '[CreateUpdateMovieService]';

  private _connection: Connection;
  private _movieRepo: Repository<MovieEntity>;
  private _movieCastRepo: Repository<MovieCastEntity>;
  private _genreRepo: Repository<GenreEntity>;

  constructor(private _typeOrmService: TypeORMService,
              private _tmdbProvider: TmdbProvider,
              private _personService: PersonService) {
  }

  public $afterRoutesInit(): void | Promise<any> {
    this._connection = this._typeOrmService.get();

    this._movieRepo = this._connection.manager.getRepository(MovieEntity);
    this._movieCastRepo = this._connection.manager.getRepository(MovieCastEntity);
    this._genreRepo = this._connection.manager.getRepository(GenreEntity);
  }

  public async handle(data: LibraryScanResult, library: LibraryEntity) {
    const metadata: MovieMetadata = data.metadata;
    let movieEntity = await this.getMovieByPath(data.dir.path);

    movieEntity = this.mapProperties(movieEntity, metadata, data.dir.path, library);
    movieEntity = await this._movieRepo.save(movieEntity);

    if (metadata.images && metadata.images.length > 0) {
      movieEntity = await this.attachImages(metadata.images, movieEntity);
      movieEntity = await this._movieRepo.save(movieEntity);
    }

    if (metadata.genres && metadata.genres.length > 0) {
      movieEntity = await this.attachGenres(metadata.genres, movieEntity);

      try {
        movieEntity = await this._movieRepo.save(movieEntity);
      } catch (e) {
        if (Number(e.code) === 23505) {
          // unique violation, ignore.
        } else {
          $log.error(`${this.logPrefix}: ${e.message}`);
        }
      }
    }

    if (movieEntity.theMovieDbId) {
      movieEntity = await this.attachCast(movieEntity);

      await this._movieRepo.save(movieEntity);
    }


    return movieEntity;
  }

  private async attachCast(movieEntity: MovieEntity) {
    const cast = await this._tmdbProvider.getMovieCastByTmdbId(movieEntity.theMovieDbId);

    if (!movieEntity.cast) {
      movieEntity.cast = [];
    }

    for (const role of cast) {
      if (!role.tmdbId) {
        continue;
      }

      const person = await this._personService.findOrCreateByTmdbId(role.tmdbId);

      let movieCastEntity = new MovieCastEntity();

      try {
        movieCastEntity = await this._movieCastRepo.findOneOrFail({where: {role: role.role, person}});
      } catch (e) {
        movieCastEntity.role = role.role;
        movieCastEntity.order = role.order;
        movieCastEntity.person = person;
        movieCastEntity.movie = movieEntity;
      }

      movieEntity.cast.push(movieCastEntity);
    }

    await this._movieCastRepo.save(movieEntity.cast);

    return movieEntity;
  }

  private mapProperties(movieEntity: MovieEntity, metadata: MovieMetadata, path: string, library: LibraryEntity) {
    movieEntity.title = metadata.title;
    movieEntity.path = path;
    movieEntity.runtime = metadata.runtime;
    movieEntity.plot = metadata.plot;
    movieEntity.imdbId = metadata.imdbId;
    movieEntity.theMovieDbId = metadata.theMovieDbId;
    movieEntity.year = metadata.year;
    movieEntity.tagline = metadata.tagline;
    movieEntity.library = library;

    return movieEntity;
  }

  private async getMovieByPath(path: string): Promise<MovieEntity> {
    let movieEntity;

    try {
      movieEntity = await this._movieRepo.findOneOrFail({where: {path}});
    } catch (e) {
      movieEntity = new MovieEntity();
    }

    return movieEntity;
  }

  private async attachImages(images: ImageRequest[], movieEntity: MovieEntity): Promise<MovieEntity> {
    const download = async (type: string, ext: string, request: AxiosRequestConfig) => {
      return streamToFile(`${movieEntity.uuid}-${type}.${ext}`, StorageDir.IMAGES, request);
    };

    for (const image of images) {
      let mimeType;

      try {
        mimeType = await getMimeType(image.request);
      } catch (e) {
        $log.warn(`${this.logPrefix}: Could not request mimeType`, {
          movie: movieEntity.title,
          imageType: image.type,
          imageUrl: `${image.request.url}${image.request.params}`,
          errorType: e.name,
        });

        continue;
      }

      const ext = extension(mimeType);
      if (!ext) continue;

      if (image.type === ImageType.POSTER && !movieEntity.poster) {
        try {
          movieEntity.poster = await download('poster', ext, image.request);
        } catch (e) {
          const error = e as AxiosError;

          $log.warn(`${this.logPrefix}: could not download poster`, {
            name: error.name,
            code: error.code,
          });

          continue;
        }
      }

      if (image.type === ImageType.BACKDROP && !movieEntity.backdrop) {
        try {
          movieEntity.backdrop = await download('backdrop', ext, image.request);
        } catch (e) {
          const error = e as AxiosError;

          $log.warn(`${this.logPrefix}: could not download backdrop`, {
            name: error.name,
            code: error.code,
          });
        }
      }
    }

    return movieEntity;
  }

  private async attachGenres(genres: string[], movieEntity: MovieEntity) {
    const findOrCreate: Promise<GenreEntity>[] = genres.map(async (genre) => {
      try {
        return await this._genreRepo.findOneOrFail({where: {name: genre}});
      } catch (e) {
        const entity = this._genreRepo.create();
        entity.name = genre;

        return await this._genreRepo.save(entity);
      }
    });

    movieEntity.genres = await Promise.all(findOrCreate);

    return movieEntity;
  }

}
