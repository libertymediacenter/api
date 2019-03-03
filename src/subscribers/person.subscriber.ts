import { EntitySubscriberInterface, EventSubscriber, InsertEvent } from 'typeorm';
import { PersonEntity } from '../entities/person.entity';
import { slugify } from '../utils/slugify';

@EventSubscriber()
export class PersonSubscriber implements EntitySubscriberInterface {

  public listenTo(): Function {
    return PersonEntity;
  }

  public beforeInsert(event: InsertEvent<PersonEntity>): Promise<any> | void {
    let id = event.entity.imdbId || event.entity.tmdbId || Date.now();

    event.entity.slug = slugify(`${event.entity.name} ${id}`);
  }

}
