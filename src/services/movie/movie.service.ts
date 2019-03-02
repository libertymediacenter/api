import { AfterRoutesInit } from '@tsed/common';
import { Service } from '@tsed/di';
import { TypeORMService } from '@tsed/typeorm';
import { Connection, Like, Repository } from 'typeorm';
import { $log } from 'ts-log-debug';
import { MovieEntity } from '../../entities/media/movie.entity';

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

  public async paginate(opts?: PaginationOptions) {
    const take = opts.take || 30;
    const skip = opts.skip || 0;
    const keyword = opts.keyword || '';

    const [result, total] = await this._movieRepo.findAndCount({
      where: {name: Like('%' + keyword + '%')},
      order: {createdAt: 'DESC'},
      take,
      skip,
    });

    return {
      data: result,
      count: total,
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
