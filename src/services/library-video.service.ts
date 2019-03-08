import { AfterRoutesInit } from '@tsed/common';
import { Service } from '@tsed/di';
import { TypeORMService } from '@tsed/typeorm';
import { Connection, Repository } from 'typeorm';
import { LibraryType } from '../entities/library.entity';
import { MovieMediaEntity } from '../entities/media/movie/movie-media.entity';
import { LibraryService } from './library.service';
import { MovieService } from './movie/movie.service';

@Service()
export class LibraryVideoService implements AfterRoutesInit {
  private _connection: Connection;
  private _movieMediaRepo: Repository<MovieMediaEntity>;

  constructor(private typeOrmService: TypeORMService,
              private libraryService: LibraryService,
              private movieService: MovieService) {

  }

  public $afterRoutesInit(): void | Promise<any> {
    this._connection = this.typeOrmService.get();
    this._movieMediaRepo = this._connection.getRepository(MovieMediaEntity);
  }

  public async getVideo(librarySlug: string, videoSlug: string): Promise<VideoItem> {
    const library = await this.libraryService.findBySlug(librarySlug);

    if (library.type === LibraryType.Movie) {
      const movie = await this.movieService.findBySlug(videoSlug);

      const media = await this._movieMediaRepo.findOneOrFail({where: {movie}});

      return {
        path: media.path,
        size: media.size,
        height: media.height,
        width: media.width,
      };
    }
  }
}

export interface VideoItem {
  path: string;
  size: number;
  height: number;
  width: number;
}