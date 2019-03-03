import { Description } from '@tsed/swagger';
import {
  Column,
  CreateDateColumn,
  Entity,
  JoinColumn, JoinTable, ManyToMany, ManyToOne,
  PrimaryGeneratedColumn,
  UpdateDateColumn,
} from 'typeorm';
import { LibraryEntity } from '../library.entity';
import { IgnoreProperty, Property } from '@tsed/common';
import { IMovie } from '../../interfaces/media';
import { GenreEntity } from '../genre.entity';
import { PersonEntity } from '../person.entity';

@Entity({name: 'movies'})
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

  @ManyToMany(type => GenreEntity)
  @JoinTable({name: 'movie_genre'})
  genres?: GenreEntity[];

  @ManyToMany(type => PersonEntity)
  @JoinTable({name: 'movie_person'})
  persons?: PersonEntity[];

  @ManyToOne(type => LibraryEntity, library => library.movies)
  @JoinColumn({name: 'library_uuid', referencedColumnName: 'uuid'})
  library?: LibraryEntity;
}
