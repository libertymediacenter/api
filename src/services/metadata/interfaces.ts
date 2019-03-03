import { AxiosRequestConfig } from 'axios';
import { Stream } from 'stream';
import { IMovie, IPerson } from '../../interfaces/media';
import { Image, ImageRequest } from './providers/provider.interface';

export interface ImageStream {
  mimeType: string;
  data: Stream;
}

export interface MovieMetadata extends IMovie {
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
