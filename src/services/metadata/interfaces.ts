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
