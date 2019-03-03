import { EntitySubscriberInterface, EventSubscriber, InsertEvent } from 'typeorm';
import { MovieEntity } from '../entities/media/movie.entity';
import { slugify } from '../utils/slugify';

@EventSubscriber()
export class MovieSubscriber implements EntitySubscriberInterface {

  public listenTo(): Function {
    return MovieEntity;
  }

  public beforeInsert(event: InsertEvent<MovieEntity>): Promise<any> | void {
    event.entity.slug = slugify(`${event.entity.title} ${event.entity.year}`);
  }

}
