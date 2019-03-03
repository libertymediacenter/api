import { AfterRoutesInit } from '@tsed/common';
import { Service } from '@tsed/di';
import { TypeORMService } from '@tsed/typeorm';
import { AxiosRequestConfig } from 'axios';
import { Connection, Repository } from 'typeorm';
import { PersonEntity } from '../entities/person.entity';
import { StorageDir, streamToFile } from '../utils/storage.utils';
import { PersonMetadata } from './metadata/interfaces';
import { getMimeType } from './metadata/providers/fetch-stream';
import { TmdbProvider } from './metadata/providers/tmdb/tmdb.provider';
import { $log } from 'ts-log-debug';
import { extension } from 'mime-types';

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

    let personEntity = new PersonEntity();

    personEntity.name = person.name;
    personEntity.bio = person.bio;
    personEntity.imdbId = person.imdbId;
    personEntity.tmdbId = person.tmdbId;

    personEntity = await this._personRepo.save(personEntity);

    personEntity.image = await this.fetchImage(person, personEntity.uuid);

    await this._personRepo.save(personEntity);

    return personEntity;
  }

  private async fetchImage(metadata: PersonMetadata, personUuid: string) {
    const download = async (type: string, ext: string, request: AxiosRequestConfig) => {
      return streamToFile(`${personUuid}-${type}.${ext}`, StorageDir.IMAGES, request);
    };

    let imagePath = null;

    try {
      const mimeType = await getMimeType(metadata.imageRequest);
      if (mimeType) {

        const ext = extension(mimeType);
        if (!ext) return;

        imagePath = await download('portrait', ext, metadata.imageRequest);
      }
    } catch (e) {
      $log.warn(`[PersonService]: Could not get image`, {
        httpCode: e.statusCode,
      });
    }

    return imagePath;
  }

}
