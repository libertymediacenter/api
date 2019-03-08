import { EntitySubscriberInterface, EventSubscriber, InsertEvent } from 'typeorm';
import { SeriesEntity } from '../entities/media/tv/series.entity';
import { slugify } from '../utils/slugify';

@EventSubscriber()
export class SeriesSubscriber implements EntitySubscriberInterface {

  public listenTo(): Function {
    return SeriesEntity;
  }

  public beforeInsert(event: InsertEvent<SeriesEntity>): Promise<any> | void {
    event.entity.slug = slugify(`${event.entity.title} ${event.entity.startYear} ${event.entity.endYear}`);
  }

}
