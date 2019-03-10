import { Property, Required, Schema } from '@tsed/common';
import {
  Column,
  CreateDateColumn,
  Entity,
  OneToMany,
  PrimaryGeneratedColumn,
  UpdateDateColumn,
} from 'typeorm';
import { IPerson } from '../interfaces/media';
import { MovieCastEntity } from './media/movie/movie-cast.entity';

@Schema({title: 'person'})
@Entity({name: 'persons'})
export class PersonEntity implements IPerson {
  @PrimaryGeneratedColumn('uuid')
  uuid: string;

  @Column('citext')
  @Property()
  @Required()
  name: string;

  @Column('citext', {unique: true})
  slug: string;

  @Column('text', {nullable: true})
  @Property()
  bio: string;

  @Column('text', {name: 'imdb_id', nullable: true})
  @Property()
  imdbId: string;

  @Column('bigint', {name: 'tmdb_id', nullable: true})
  @Property()
  tmdbId: number;

  @Column('text', {nullable: true})
  @Property()
  image: string;

  @CreateDateColumn({name: 'created_at', type: 'timestamptz'})
  createdAt: Date;

  @UpdateDateColumn({name: 'updated_at', type: 'timestamptz'})
  updatedAt: Date;

  /* Relations */

  @OneToMany(type => MovieCastEntity, movieCastEntity => movieCastEntity.person)
  movieRoles: MovieCastEntity[];
}
