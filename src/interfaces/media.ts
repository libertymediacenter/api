export interface IRating {
  provider: string;
  displayScore: string;
  score: number;
}

export interface IMovieMetadata extends IMovie {
  posterUrl?: string;
  backdropUrl?: string;
}

export interface ISeriesMetadata extends ISeries {
  poster?: string;
}

export interface IEpisodeMetadata extends IEpisode {
  poster?: string;
}

export interface IMovie {
  title: string;
  path?: string;
  slug?: string;
  year?: number;
  runtime?: number;
  tagline?: string;
  plot?: string;
  imdbId?: string;
  theMovieDbId?: number;
  ratings?: IRating[];
}

export interface ISeries {
  title: string;
  path?: string;
  slug?: string;
  startYear?: number;
  endYear?: number | null;
  tagline?: string;
  summary?: string;
  network?: string;
  runtime?: number;
  theTvDbId?: number;
  imdbId?: string;
  ratings?: IRating[];
}

export interface IEpisode {
  title: string;
  path?: string;
  slug?: string;
  airDate?: Date;
  runtime?: number;
  theTvDbId?: number;
  imdbId?: string;
  ratings?: IRating[];
}

export interface IPerson {
  name: string;
  image?: string;
  bio?: string;
  imdbId?: string;
  tmdbId?: number;
}
