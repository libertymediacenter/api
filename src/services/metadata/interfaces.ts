import { Stream } from 'stream';
import { IMovie } from '../../interfaces/media';
import { Image, ImageRequest } from './providers/provider.interface';

export interface ImageStream {
  mimeType: string;
  data: Stream;
}

export interface MovieMetadata extends IMovie {
  images?: ImageRequest[];
  genres?: string[];
}

export interface PersonMetadata {
  name: string;
  bio: string;
  imdbId?: string;
  tmdbId?: string;
  photoUrl?: string;
}

export interface CastMetadata {
  tmdbId: number;
  role: string;
  order: number;
}
