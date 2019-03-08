import { AfterRoutesInit } from '@tsed/common';
import { Service } from '@tsed/di';
import { TypeORMService } from '@tsed/typeorm';
import { Connection, Repository } from 'typeorm';
import { LibraryEntity } from '../../entities/library.entity';
import { SeriesEntity } from '../../entities/media/tv/series.entity';
import { LibraryScanResult } from '../library-scanner.service';
import { TvMetadata } from '../metadata/interfaces';

@Service()
export class CreateUpdateSeriesService implements AfterRoutesInit {
  private _connection: Connection;
  private _seriesRepo: Repository<SeriesEntity>;

  constructor(private typeOrmService: TypeORMService) {
  }

  public $afterRoutesInit(): void | Promise<any> {
    this._connection = this.typeOrmService.get();
    this._seriesRepo = this._connection.manager.getRepository(SeriesEntity);
  }

  public async handle(data: LibraryScanResult, library: LibraryEntity): Promise<SeriesEntity> {
    let seriesEntity = await this.findOrCreateByPath(data.dir.path);

    const metadata: TvMetadata = data.metadata;

    seriesEntity = this.mapProperties(seriesEntity, metadata, library);
    await this._seriesRepo.save(seriesEntity);

    return seriesEntity;
  }

  private async findOrCreateByPath(path: string) {
    try {
      return await this._seriesRepo.findOneOrFail({where: path});
    } catch (e) {
      const seriesEntity = this._seriesRepo.create();
      seriesEntity.path = path;

      return seriesEntity;
    }
  }

  private mapProperties(seriesEntity: SeriesEntity, metadata: TvMetadata, library: LibraryEntity) {
    let series = seriesEntity;

    series.title = metadata.title;
    series.startYear = metadata.startYear;
    series.endYear = metadata.endYear;
    series.summary = metadata.summary;
    series.network = metadata.network;
    series.runtime = metadata.runtime;
    series.library = library;

    return series;
  }
}
