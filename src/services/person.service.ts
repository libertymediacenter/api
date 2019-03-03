import { AfterRoutesInit } from '@tsed/common';
import { Service } from '@tsed/di';
import { TypeORMService } from '@tsed/typeorm';
import { Connection, Repository } from 'typeorm';
import { PersonEntity } from '../entities/person.entity';
import { TmdbProvider } from './metadata/providers/tmdb/tmdb.provider';

@Service()
export class PersonService implements AfterRoutesInit {
  private _connection: Connection;
  private _personRepo: Repository<PersonEntity>;

  constructor(private _typeOrmService: TypeORMService,
              private _tmdbProvider: TmdbProvider) {
  }

  public $afterRoutesInit(): void | Promise<any> {
    this._connection = this._typeOrmService.get();
    this._personRepo = this._connection.getRepository(PersonEntity);
  }

  public async findOrCreateByTmdbId(tmdbId: number): Promise<PersonEntity> {
    let personEntity: PersonEntity;

    try {
      personEntity = await this._personRepo.findOneOrFail({where: {tmdbId}});
    } catch (e) {
      personEntity = await this.create(tmdbId);
    }

    return personEntity;
  }

  private async create(tmdbId: number): Promise<PersonEntity> {
    const person = await this._tmdbProvider.getPerson(tmdbId);

    const personEntity = new PersonEntity();

    personEntity.name = person.name;
    personEntity.bio = person.bio;
    personEntity.imdbId = person.imdbId;
    personEntity.tmdbId = person.tmdbId;

    return this._personRepo.save(personEntity);
  }

}
