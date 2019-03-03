import { Controller, Get, PathParams, QueryParams, Status } from '@tsed/common';
import { Name } from '@tsed/swagger';
import { MovieEntity } from '../../entities/media/movie.entity';
import { Collection } from '../../interfaces/response';
import { MovieService } from '../../services/movie/movie.service';

@Name('Movie')
@Controller('/movies')
export class MovieController {

  constructor(private readonly movieService: MovieService) {
  }

  @Get('')
  @Status(200)
  public async index(@QueryParams('perPage') perPage: number,
                     @QueryParams('page') page: number): Promise<Collection<MovieEntity[]>> {
    const take = perPage || 30;
    let pageNo = 0;

    if (page > 0) {
      pageNo = page - 1;
    }

    const skip = pageNo * take;

    const data = await this.movieService.paginate({skip, take});

    return {
      data: data.data,
      total: data.count,
      perPage: take,
      pages: Math.ceil((data.count / take)),
    };
  }

  @Get('/:slug')
  @Status(200)
  public get(@PathParams('slug') slug: string): Promise<MovieEntity> {
    return this.movieService.findBySlug(slug);
  }

}
