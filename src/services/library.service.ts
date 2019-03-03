import { AfterRoutesInit } from '@tsed/common';
import { Service } from '@tsed/di';
import { TypeORMService } from '@tsed/typeorm';
import { Connection, Repository } from 'typeorm';
import { LibraryEntity, LibraryType } from '../entities/library.entity';
import { MovieEntity } from '../entities/media/movie.entity';
import { EpisodeEntity } from '../entities/media/tv/episode.entity';
import { SeriesEntity } from '../entities/media/tv/series.entity';
import { LibraryScanResult } from './library-scanner.service';
import { CreateUpdateMovieService } from './movie/create-update-movie.service';

@Service()
export class LibraryService implements AfterRoutesInit {
  private _connection: Connection;

  private _libraryRepo: Repository<LibraryEntity>;

  constructor(private typeOrmService: TypeORMService,
              private createUpdateMovieService: CreateUpdateMovieService) {
  }

  public $afterRoutesInit(): void | Promise<any> {
    this._connection = this.typeOrmService.get();
    this._libraryRepo = this._connection.manager.getRepository(LibraryEntity);
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
        return await this.createUpdateMovieService.handle(data, library);
      default:
        throw new Error('Library type unknown!');
    }
  }
}
