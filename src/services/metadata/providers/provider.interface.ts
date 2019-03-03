import { AxiosRequestConfig } from 'axios';
import { IEpisode, IMovie, ISeries } from '../../../interfaces/media';
import { ImageStream, MovieMetadata } from '../interfaces';

export interface MetadataOptions {
  type: 'movie' | 'series' | 'episode';
  year?: number;
  fetchPoster: boolean;
  fetchBackdrop: boolean;
}

export interface MetadataProvider {
  getByTitle(title: string, options: MetadataOptions): Promise<MovieMetadata>;
}

export interface PersonDataProvider {

}

export interface ImageProviderOptions extends MetadataOptions {

}

export enum ImageType {
  BACKDROP = 'backdrop',
  POSTER = 'poster',
}

export interface Image {
  stream: ImageStream;
  type: ImageType;
}

export interface ImageRequest {
  request: AxiosRequestConfig;
  type: ImageType;
}
