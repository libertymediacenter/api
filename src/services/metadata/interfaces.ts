import { AxiosRequestConfig } from 'axios';
import { Stream } from 'stream';
import { IMovie, IPerson, ISeries } from '../../interfaces/media';
import { Image, ImageRequest } from './providers/provider.interface';
import { BelongsToCollection } from './providers/tmdb/responses';

export interface ImageStream {
  mimeType: string;
  data: Stream;
}

export interface MovieMetadata extends IMovie {
  images?: ImageRequest[];
  genres?: string[];
  tmdbCollection?: BelongsToCollection
}

export interface TvMetadata extends ISeries {
  images?: ImageRequest[];
  genres?: string[];
}

export interface CastMetadata {
  tmdbId: number;
  role: string;
  order: number;
}

export interface PersonMetadata extends IPerson {
  imageRequest?: AxiosRequestConfig;
}
