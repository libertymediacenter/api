import { JsonProperty, Property, Required, Schema } from '@tsed/common';
import {
  Column,
  CreateDateColumn,
  Entity, Index,
  OneToMany,
  PrimaryGeneratedColumn,
  UpdateDateColumn,
} from 'typeorm';
import { ILibrary } from '../interfaces/library';
import { MovieEntity } from './media/movie/movie.entity';
import { SeriesEntity } from './media/tv/series.entity';

export enum LibraryType {
  AudioBook = 'audiobook',
  Ebook = 'ebook',
  Movie = 'movie',
  Other = 'other',
  Series = 'series',
  Sports = 'sports',
}

@Schema({title: 'library'})
@Entity({name: 'libraries'})
@Index('library_slug_type_key', ['slug', 'type'], {unique: true})
export class LibraryEntity implements ILibrary {
  @PrimaryGeneratedColumn('uuid', {name: 'uuid'})
  uuid: string;

  @Column('citext')
  @Property()
  @Required()
  title: string;

  @Column('citext')
  @Property()
  slug: string;

  @Column('enum', {enum: LibraryType})
  @Required()
  @Property()
  type: LibraryType;

  @Column('text', {name: 'meta_lang', default: 'en'})
  @Required()
  @Property()
  metadataLang: string;

  @Column('text')
  @Required()
  path: string;

  @CreateDateColumn({name: 'created_at', type: 'timestamptz'})
  createdAt: Date;

  @UpdateDateColumn({name: 'updated_at', type: 'timestamptz'})
  updatedAt: Date;

  /* Relations */

  @OneToMany(type => MovieEntity, movieEntity => movieEntity.library)
  movies: MovieEntity[];

  @OneToMany(type => SeriesEntity, seriesEntity => seriesEntity.library)
  series: SeriesEntity[];
}
