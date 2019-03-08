import { Service } from '@tsed/di';
import { AxiosRequestConfig } from 'axios';
import { IEpisode, IRating, ISeries } from '../../../../interfaces/media';
import { HttpClient } from '../../../http-client';
import { MovieMetadata } from '../../interfaces';
import { generateImageStreamRequest } from '../fetch-stream';

import { ImageRequest, ImageType, MetadataOptions, MetadataProvider } from '../provider.interface';
import { OmdbEpisode, OmdbMovie, OmdbRating, OmdbSeries } from './responses';
import * as slug from 'slug';
import { $log } from 'ts-log-debug';

export interface OmdbProviderConfig {
  baseUrl: string;
  apiKey: string;
  posterApi: PosterApiConfig;
}

export interface PosterApiConfig {
  baseUrl: string;
  apiKey?: string;
  height: number;
}

@Service()
export class OmdbProvider implements MetadataProvider {
  protected config: OmdbProviderConfig = {
    baseUrl: process.env.METADATA_OMDB_API_URL,
    apiKey: process.env.METADATA_OMDB_API_KEY,
    posterApi: {
      baseUrl: process.env.METADATA_OMDB_POSTER_URL,
      height: Number(process.env.METADATA_OMDB_POSTER_SIZE),
    },
  };

  constructor() {
  }

  public async getByTitle(title: string, options: MetadataOptions): Promise<MovieMetadata> {
    if (options.type !== 'movie') {
      return null;
    }

    const omdbSlugify = (subject) => {
      return subject.toString().toLowerCase()
        .replace(/\s:/g, '%3A')
        .replace(/\s-/g, '+')           // Replace dashes with +
        .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
        .replace(/--+/g, '-')         // Replace multiple - with single -
        .replace(/^-+/, '')             // Trim - from start of text
        .replace(/-+$/, '');            // Trim - from end of text
    };

    const query = {t: omdbSlugify(title), type: options.type};
    if (options.year) {
      Object.assign(query, {y: options.year});
    }

    try {
      let item = await this.fetch(query);

      if (options.fetchPoster) {
        item.images = this.fetchPoster(item.imdbId);
      }

      return item;
    } catch (e) {
      return null;
    }
  }

  private fetchPoster(imdbId: string): ImageRequest[] {
    const images: ImageRequest[] = [];

    images.push({
      type: ImageType.POSTER,
      request: this.getPosterByImdbId(imdbId),
    });

    return images;
  }

  private getPosterByImdbId(imdbId: string): AxiosRequestConfig {
    const config: AxiosRequestConfig = {
      url: this.config.posterApi.baseUrl,
      params: {
        i: imdbId,
        h: this.config.posterApi.height,
        apiKey: this.config.posterApi.apiKey || this.config.apiKey,
      },
    };

    const request = generateImageStreamRequest(config);
    if (!request.url) {
      $log.debug('[PROVIDER](OMDB): No url for request!', request);
      return null;
    }

    return request;
  }

  private async fetch(query): Promise<MovieMetadata> {
    let res;

    Object.assign(query, {apiKey: this.config.apiKey});

    switch (query.type) {
      case 'movie':
        res = await this.get<OmdbMovie>(query);
        break;
      case 'series':
        res = await this.get<OmdbSeries>(query);
        break;
      case 'episode':
        res = await this.get<OmdbEpisode>(query);
        break;
      default:
        throw Error('Could not parse type!');
    }

    return this.deserialize(res);
  }

  private deserialize(input: OmdbMovie | OmdbSeries | OmdbEpisode): MovieMetadata | ISeries | IEpisode {
    switch (input.Type) {
      case 'movie':
        return this.serializeMovie(<OmdbMovie>input);
      case 'series':
        return this.serializeSeries(<OmdbSeries>input);
      case 'episode':
        return this.serializeEpisode(<OmdbEpisode>input);
      default:
        return;
    }
  }

  private serializeRating(input: OmdbRating[]): IRating[] {
    return input.map(r => {
      let score = r.Value;
      let provider = '';

      const splitBySlash = (subject: string) => {
        return subject.split('/')[0];
      };

      if (r.Source.toLowerCase() === 'internet movie database') {
        provider = 'imdb';
        score = splitBySlash(r.Value);
      }

      if (r.Source.toLowerCase() === 'metacritic') {
        provider = 'metacritic';
        score = (Number(splitBySlash(r.Value)) / 100).toFixed(1);
      }

      if (r.Source.toLowerCase() === 'rotten tomatoes') {
        provider = 'rottentomatoes';
        score = r.Value.replace('%', '');
      }

      return {
        provider,
        displayScore: r.Value,
        score: Number(score),
      } as IRating;
    });
  }

  private serializeMovie(input: OmdbMovie): MovieMetadata {
    const omdbRatings = input.Ratings.slice();
    const ratings: IRating[] = this.serializeRating(omdbRatings);

    return {
      title: input.Title,
      year: Number(input.Year),
      runtime: this.getRuntime(input.Runtime),
      plot: input.Plot,
      imdbId: input.imdbID,
      ratings,
      images: [],
      genres: input.Genre.split(','),
    };
  }

  private serializeSeries(input: OmdbSeries): ISeries {
    const years = input.Year.split('-');
    const omdbRatings = input.Ratings.slice();
    const ratings: IRating[] = this.serializeRating(omdbRatings);

    return {
      title: input.Title,
      startYear: Number(years[0]),
      endYear: Number(years[1]),
      summary: input.Plot,
      imdbId: input.imdbID,
      runtime: this.getRuntime(input.Runtime),
      ratings,
    };
  }

  private serializeEpisode(input: OmdbEpisode): IEpisode {
    const omdbRatings = input.Ratings.slice();
    const ratings: IRating[] = this.serializeRating(omdbRatings);

    return {
      title: input.Title,
      airDate: new Date(input.Released),
      runtime: this.getRuntime(input.Runtime),
      imdbId: input.imdbID,
      ratings,
    };
  }

  private getRuntime(input: string) {
    const runtime = Number(input.replace(' min', ''));

    if (typeof runtime === 'number' && !isNaN(runtime)) {
      return runtime;
    }

    return null;
  }

  private async get<T>(params) {
    const paramsSerializer = (params: any) => {
      if (!params) {
        return '';
      }

      return Object.keys(params)
        .map(k => encodeURIComponent(k) + '=' + slug(String(params[k]), {
          replacement: '+',
          lower: true,
        }))
        .join('&');
    };

    const axios = HttpClient.getInstance();

    const res = await axios.get<T>(this.config.baseUrl, {
      paramsSerializer,
      params,
    });

    return res.data;
  }
}
