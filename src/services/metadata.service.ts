import { OnInit, Service } from '@tsed/di';
import { IEpisode, IMovie, ISeries } from '../interfaces/media';
import { dirTitleRegex } from '../utils/regex.utils';
import { MovieMetadata } from './metadata/interfaces';
import { OmdbProvider } from './metadata/providers/omdb/omdb.provider';
import { MetadataOptions, MetadataProvider } from './metadata/providers/provider.interface';
import { TmdbProvider } from './metadata/providers/tmdb/tmdb.provider';
import { $log } from 'ts-log-debug';

interface IProvider {
  provider: MetadataProvider;
  priority: number;
}

@Service()
export class MetadataService implements OnInit {
  private readonly _providers: IProvider[] = [];

  constructor(private omdbProvider: OmdbProvider,
              private tmdbProvider: TmdbProvider) {
    this._providers.push(...[
      {
        provider: this.omdbProvider,
        priority: 1,
      },
      {
        provider: this.tmdbProvider,
        priority: 2,
      },
    ]);
  }

  public $onInit(): Promise<any> | void {
    this._providers.sort(((a, b) => {
      return a.priority - b.priority;
    }));
  }

  public async getByTitle(title: string, options: MetadataOptions): Promise<MovieMetadata> {
    const regexTitle = dirTitleRegex(title);

    Object.assign(options, {
      year: regexTitle.year,
    });

    const results: MovieMetadata[] = [];

    for (const [i, provider] of this._providers.entries()) {
      try {
        const providerQuery = await provider.provider.getByTitle(String(regexTitle.title), options);

        if (providerQuery) {
          // Reject metadata if year is not matching input.
          if (options.year && (providerQuery.year !== options.year)) {
            return null;
          }

          results.push(providerQuery);
        }
      } catch (e) {
        $log.error('[MetadataService]: could not use getByTitle', e);

        return;
      }
    }

    let movie: MovieMetadata = {} as MovieMetadata;
    results.map(x => this.mergeMetadata(movie, x));

    return movie;
  }

  private mergeMetadata(target: MovieMetadata, source: MovieMetadata): MovieMetadata {
    for (const key of Object.keys(source)) {
      const prop = target[key];
      const sourceProp = source[key];

      if (!sourceProp) {
        return;
      }

      if (!Array.isArray(prop)) {
        target[key] = sourceProp;
      } else {
        prop.push(...source[key]);
      }
    }

    return target;
  }
}
