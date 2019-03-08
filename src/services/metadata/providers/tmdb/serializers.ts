import * as moment from 'moment';
import { MovieMetadata, TvMetadata } from '../../interfaces';
import { Genre, Movie, TV } from './responses';

const serializeGenres = (genres: Genre[]): string[] => {
  return genres.map(x => x.name);
};

export const serializeMovie = (movie: Movie): MovieMetadata => {
  return {
    title: movie.title,
    year: moment(movie.release_date, 'YYYY-MM-DD').year(),
    runtime: movie.runtime,
    plot: movie.overview,
    tagline: movie.tagline,
    imdbId: movie.imdb_id,
    theMovieDbId: movie.id,
    ratings: [{provider: 'tmdb', displayScore: movie.vote_average.toString(), score: movie.vote_average}],
    genres: serializeGenres(movie.genres),
    images: [],
    tmdbCollection: movie.belongs_to_collection,
  };
};

export const serializeSeries = (series: TV): TvMetadata => {
  return {
    title: series.name,
    startYear: moment(series.first_air_date, 'YYYY-MM-DD').year(),
    endYear: moment(series.last_air_date, 'YYYY-MM-DD').year(),
    summary: series.overview,
    network: series.networks[0].name,
    runtime: series.episode_run_time[0],
    theTvDbId: series.id,
    images: [],
    genres: serializeGenres(series.genres),
  };
};
