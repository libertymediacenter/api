import { EntitySubscriberInterface, EventSubscriber, InsertEvent } from 'typeorm';
import { MovieCollectionEntity } from '../entities/media/movie/movie-collection.entity';
import { MovieEntity } from '../entities/media/movie/movie.entity';
import { slugify } from '../utils/slugify';

@EventSubscriber()
export class MovieCollectionSubscriber implements EntitySubscriberInterface {

  public listenTo(): Function {
    return MovieCollectionEntity;
  }

  public beforeInsert(event: InsertEvent<MovieCollectionEntity>): Promise<any> | void {
    event.entity.slug = slugify(`${event.entity.name}`);
  }

}
