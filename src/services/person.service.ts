import { AfterRoutesInit } from '@tsed/common';
import { Service } from '@tsed/di';
import { TypeORMService } from '@tsed/typeorm';
import { Connection, Repository } from 'typeorm';
import { PersonEntity } from '../entities/person.entity';

@Service()
export class PersonService implements AfterRoutesInit {
  private _connection: Connection;
  private _personRepo: Repository<PersonEntity>;

  constructor(private _typeOrmService: TypeORMService) {}

  public $afterRoutesInit(): void | Promise<any> {
    this._connection = this._typeOrmService.get();
    this._personRepo = this._connection.getRepository(PersonEntity);
  }

  // TODO: Find or create by criteria

}
