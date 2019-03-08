import { AfterRoutesInit } from '@tsed/common';
import { Service } from '@tsed/di';
import { TypeORMService } from '@tsed/typeorm';
import { Connection, Like, Repository } from 'typeorm';
import { $log } from 'ts-log-debug';
import { MovieEntity } from '../../entities/media/movie/movie.entity';
import { Collection } from '../../interfaces/response';

export interface PaginationOptions {
  skip?: number;
  take?: number;
  keyword?: string;
}

@Service()
export class MovieService implements AfterRoutesInit {
  private _connection: Connection;
  private _movieRepo: Repository<MovieEntity>;

  constructor(private typeORMService: TypeORMService) {
  }

  $afterRoutesInit() {
    this._connection = this.typeORMService.get();
    this._movieRepo = this._connection.manager.getRepository(MovieEntity);
  }

  public async paginateV2(opts?: PaginationOptions): Promise<Collection<MovieEntity[]>> {
    const take = opts.take || 0;

    const [result, count] = await this._movieRepo
      .createQueryBuilder('movies')
      .leftJoinAndSelect('movies.collection', 'collection')
      .leftJoinAndSelect('movies.genres', 'genres')
      .leftJoinAndSelect('movies.cast', 'cast')
      .innerJoinAndSelect('cast.person', 'person')
      .where([{name: Like('%' + opts.keyword || '' + '%')}])
      .skip(opts.skip || 30)
      .take(take)
      .getManyAndCount();

    return {
      data: result,
      total: count,
      perPage: take,
      pages: Math.ceil((count / take)),
    };
  }

  public async paginate(opts?: PaginationOptions): Promise<Collection<MovieEntity[]>> {
    const take = opts.take || 30;
    const skip = opts.skip || 0;
    const keyword = opts.keyword || '';

    const [result, count] = await this._movieRepo.findAndCount({
      where: {name: Like('%' + keyword + '%')},
      order: {createdAt: 'DESC'},
      take,
      skip,
    });

    return {
      data: result,
      total: count,
      perPage: take,
      pages: Math.ceil((count / take)),
    };
  }

  public async findBySlug(slug: string): Promise<MovieEntity> {
    return this._movieRepo.findOneOrFail({where: {slug}});
  }


  public async delete(movie: MovieEntity): Promise<boolean> {
    return new Promise<boolean>((async (resolve, reject) => {
      try {
        await this._movieRepo.delete({uuid: movie.uuid});

        resolve();
      } catch (e) {
        $log.error(`Could not delete movie with uuid: ${movie.uuid}`, e);

        reject(false);
      }
    }));
  }

}
