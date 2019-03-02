import { Controller, Get, PathParams, QueryParams, Status } from '@tsed/common';
import { Name } from '@tsed/swagger';
import { MovieService } from '../../services/movie/movie.service';
import { MovieEntity } from '../../entities/media/movie.entity';

@Name('Movie')
@Controller('/movies')
export class MovieController {

  constructor(private readonly movieService: MovieService) {
  }

  @Get('')
  @Status(200)
  public index(@QueryParams('perPage') perPage: number,
               @QueryParams('page') page: number) {
    const take = perPage || 30;
    let pageNo = 0;

    if (page > 0) {
      pageNo = page;
    }

    const skip = pageNo * take;

    return this.movieService.paginate({skip, take});
  }

  @Get('/:slug')
  @Status(200)
  public get(@PathParams('slug') slug: string): Promise<MovieEntity> {
    return this.movieService.findBySlug(slug);
  }

}
