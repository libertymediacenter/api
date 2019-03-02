import * as moment from 'moment';
import { MovieMetadata } from '../../interfaces';
import { Genre, Movie } from './responses';

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
  };
};
