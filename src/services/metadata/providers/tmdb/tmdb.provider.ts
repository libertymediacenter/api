import { Service } from '@tsed/di';
import { AxiosRequestConfig } from 'axios';
import { $log } from 'ts-log-debug';
import { LibraryType } from '../../../../entities/library.entity';
import { IEpisode, IPerson, ISeries } from '../../../../interfaces/media';
import { sleep } from '../../../../utils/sleep';
import { CastMetadata, MovieMetadata, PersonMetadata } from '../../interfaces';
import { generateImageStreamRequest } from '../fetch-stream';
import {
  ImageType,
  MetadataOptions,
  MetadataProvider, PersonDataProvider,
} from '../provider.interface';
import { Cast, Configuration, Movie, MovieCreditsResponse, PersonDetailsResponse, SearchResult, TV } from './responses';
import { serializeMovie, serializeSeries } from './serializers';
import * as got from 'got';
import * as queryString from 'querystring';

@Service()
export class TmdbProvider implements MetadataProvider, PersonDataProvider {
  private readonly _baseUrl: string;
  private readonly _key: string;
  private _tmdbConf: Configuration;

  constructor() {
    this._baseUrl = process.env.METADATA_TMB_API_URL;
    this._key = process.env.METADATA_TMDB_API_KEY;

    this.get<Configuration>('/configuration')
      .then((res) => this._tmdbConf = res)
      .catch($log.error);
  }

  public async getByTitle(title: string, options: MetadataOptions): Promise<MovieMetadata | ISeries | IEpisode> {
    const res = await this.findByTitle(title, options);
    if (!res) {
      return null;
    }

    let item;

    if (options.type === 'movie') {
      item = serializeMovie(res as Movie);
    }

    if (options.type === 'series') {
      item = serializeSeries(res as TV);
    }

    if (options.fetchBackdrop && res.backdrop_path) {
      try {

        item.images.push({
          type: ImageType.BACKDROP,
          request: this.getImage(res.backdrop_path),
        });
      } catch (e) {
        $log.error(`[PROVIDER](TMDB): Could not fetch backdrop ${res.backdrop_path}`, e);
      }
    }

    if (options.fetchPoster && res.poster_path) {
      try {

        item.images.push({
          type: ImageType.POSTER,
          request: this.getImage(res.poster_path),
        });
      } catch (e) {
        $log.error(`[PROVIDER](TMDB): Could not fetch poster ${res.poster_path}`, e);
      }
    }

    return item;
  }

  public async getMovieCastByTmdbId(id: number): Promise<CastMetadata[]> {
    const credits = await this.get<MovieCreditsResponse>(`/movie/${id}/credits`);

    const cast = credits.cast.map((cast) => {
      if (cast.order < 6) {
        return {
          tmdbId: cast.id,
          role: cast.character,
          order: cast.order,
        };
      }
    }).filter(x => x);

    return cast;
  }

  public async getPerson(id: number): Promise<PersonMetadata> {
    let person: PersonDetailsResponse;

    try {
      person = await this.get<PersonDetailsResponse>(`/person/${id}`);
    } catch (e) {
      if (e.statusCode === 429) {
        await sleep(200);

        person = await this.get<PersonDetailsResponse>(`/person/${id}`);

        return null;
      }
    }

    return {
      name: person.name,
      bio: person.biography,
      imdbId: person.imdb_id,
      tmdbId: person.id,
      imageRequest: this.getImage(person.profile_path),
    };
  }

  private getImage(path: string, quality = 'original'): AxiosRequestConfig {

    const config: AxiosRequestConfig = {
      url: `${this._tmdbConf.images.base_url}${quality}${path}`,
    };

    return generateImageStreamRequest(config);
  }

  private async findByTitle(title: string, options: MetadataOptions): Promise<Movie | TV> {
    let endpoint, query;
    const language = 'en-US';

    const year = options.year;

    const getItem = async <T>(query) => {
      try {
        const searchResult = await query;

        if (searchResult.results.length > 0) {
          return this.get<T>(`${endpoint}/${searchResult.results[0].id}`);
        }
      } catch (e) {
        $log.error(e);
      }
    };

    if (options.type === LibraryType.Movie) {
      endpoint = '/movie';
      query = await this.get<SearchResult<Movie[]>>('/search/movie', {
        language,
        query: title,
        year,
      });

      return getItem<Movie>(query);
    }

    if (options.type === LibraryType.Series) {
      endpoint = '/tv';
      query = await this.get<SearchResult<TV[]>>('/search/tv', {
        language,
        query: title,
      });

      return getItem<TV>(query);
    }
  }


  private get<T>(endpoint: string, params?: object): Promise<T> {
    const parameters = {
      ...params,
      api_key: this._key,
    };

    const searchParams = queryString.stringify(parameters);

    const request = (endpoint) => {
      return got.get(`${endpoint}?${searchParams}`, {
        baseUrl: this._baseUrl,
        json: true,
      });
    };

    return new Promise<T>(((resolve, reject) => {
      request(endpoint)
        .then((res) => {
          resolve(res.body as T);
        })
        .catch(reject);
    }));
  }

}
