import { Description } from '@tsed/swagger';
import {
  Column,
  CreateDateColumn,
  Entity, Index,
  JoinColumn, JoinTable, ManyToMany, ManyToOne, OneToMany,
  PrimaryGeneratedColumn,
  UpdateDateColumn,
} from 'typeorm';
import { LibraryEntity } from '../../library.entity';
import { IgnoreProperty, Property } from '@tsed/common';
import { IMovie } from '../../../interfaces/media';
import { GenreEntity } from '../../genre.entity';
import { MovieCastEntity } from './movie-cast.entity';
import { MovieCollectionEntity } from './movie-collection.entity';
import { MovieMediaEntity } from './movie-media.entity';

@Entity({name: 'movies'})
@Index('movies_slug_library_key', ['slug', 'library'], {unique: true})
export class MovieEntity implements IMovie {
  @PrimaryGeneratedColumn('uuid', {name: 'uuid'})
  @IgnoreProperty()
  uuid: string;

  @Column('citext')
  @Property()
  title: string;

  @Column('citext', {nullable: false})
  @Property()
  slug?: string;

  @Column({nullable: true})
  @Property()
  year: number;

  @Column('text', {name: 'imdb_id', nullable: true})
  @Property()
  imdbId: string;

  @Column('integer', {name: 'tmdb_id', nullable: true})
  @Property()
  theMovieDbId: number;

  @Column('integer', {nullable: true})
  @Property()
  runtime: number;

  @Column('text', {nullable: true})
  @Property()
  tagline: string;

  @Column('text', {nullable: true})
  @Property()
  plot: string;

  @Column('text', {unique: true})
  @Property()
  @Description('Absolute path to the directory containing the movie')
  path: string;

  @Column('text', {nullable: true})
  @Property()
  poster?: string;

  @Column('text', {nullable: true})
  @Property()
  backdrop?: string;

  @CreateDateColumn({name: 'created_at', type: 'timestamptz'})
  createdAt?: Date;

  @UpdateDateColumn({name: 'updated_at', type: 'timestamptz'})
  updatedAt?: Date;

  /* Relations */

  @ManyToMany(type => GenreEntity, {eager: true})
  @JoinTable({name: 'movie_genre', joinColumn: {name: 'movie_uuid'}, inverseJoinColumn: {name: 'genre_uuid'}})
  @Property({name: 'genres', use: GenreEntity})
  genres: GenreEntity[];

  @OneToMany(type => MovieCastEntity, movieCast => movieCast.movie, {eager: true})
  @Property({name: 'cast', use: MovieCastEntity})
  cast: MovieCastEntity[];

  @OneToMany(type => MovieMediaEntity, movieMedia => movieMedia.movie)
  media: MovieMediaEntity[];

  @ManyToOne(type => LibraryEntity, library => library.movies, {eager: true})
  @JoinColumn({name: 'library_uuid', referencedColumnName: 'uuid'})
  @Property()
  library?: LibraryEntity;

  @ManyToOne(type => MovieCollectionEntity, movieCollection => movieCollection.movies)
  @JoinColumn({name: 'collection_uuid'})
  collection?: MovieCollectionEntity;
}
